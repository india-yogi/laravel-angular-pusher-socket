<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use App\Mail\SendOrderEmail;

use App\Traits\ApiResponser;
use App\Jobs\LoggingJob;
use App\Jobs\EmailJob;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

use App\Order;
use Helper;
use DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Events\OrdersFetched;

class OrderController extends Controller
{
    use ApiResponser;

    const FAILED = 'Failed';
    const SUCCESS = 'Success';
    private $LOG_SERVICE_URL;

    const STATUS_NEW = 1;
    const STATUS_SENT = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_DELETED = 5;

    // commong rules for the insert and update
    protected $rules = [
        'client_id' => 'required|integer',
        'user_id' => 'required|integer',
        'type_id' => 'required|integer',
        'model_id' => 'required|integer',
        'supplier_id' => 'integer',
        'model_name' => 'max:100|string',
        'quantity' => 'required|integer',
        'comment' => 'max:500|string',
        'status' => 'integer',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->LOG_SERVICE_URL = env("LOG_SERVICE_URL", '');
    }

    /**
     * Return full list of orders of a client
     * @return Response
     */
    public function index(Request $request)
    {
        \Log::info('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
        \Log::info('Orders fetched...');
        $client_id = (int)$request->client_id;

        $supplier = 0;
        $status = 0;
        $search = '';
        $order_by = 'created_at';
        $order = 'desc';
        $limit = 0;

        if ($request->supplier) {
            $supplier = $request->supplier;
        }

        if ($request->status) {
            $status = $request->status;
        }

        if ($request->search) {
            $search = $request->search;
        }

        if ($request->order_by) {
            $order_by = $request->order_by;
        }

        if ($request->order) {
            $order = $request->order;
        }

        if ($request->limit) {
            $limit = $request->limit;
        }

        $query = Order::where('client_id', '=', $client_id);

        if ($search) {
            if (isset($search) && !empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('model_name', 'LIKE', '%' . trim($search) . '%');
                });
            }
        }

        if (isset($supplier) && !empty($supplier)) {
            $query->where('supplier_id', $supplier);
        }

        if (isset($status) && !empty($status)) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy($order_by, $order)->paginate($limit);

        if ($orders->isEmpty()) {
            return Helper::fail(self::FAILED, []);
        }

        ////////////////////////////
        \Log::info('Dispatching OrdersFetched event...');
        event(new OrdersFetched($orders, $client_id));
        ////////////////////////////

        return Helper::success(self::SUCCESS, $orders->toArray(), 200);
    }

    /**
     * Create one new orders
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        \Log::debug($request->all());
        \Log::debug("In store method");
        $client_id = (int)$request->client_id;
        $input = $request->all();
        $action = 'add';

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Helper::fail(self::FAILED, $errors);
        }
        $input['supplier_id'] = $input['supplier_id'] ?? 0;

        // check if order with same model_id and supplier_id with status 'NEW' 1 is exist, then just
        // ad more in its requested quantity
        $order = Order::where('client_id', $client_id)->
        where('supplier_id', $input['supplier_id'])->
        where('model_id', $input['model_id'])->
        where('status', 1)->
        first();

        if (isset($order) && !empty($order)) {
            $requested_qty = $input['requested_quantity'] ?? 0;
            $qty = $input['quantity'] ?? 0;

            $input['requested_quantity'] = $requested_qty + $order->requested_quantity;
            $input['quantity'] = $qty + $order->quantity;
            $order->fill($input);
            $order->save();
            $action = 'update';
        } else {
            if (!isset($input['adaptExisting']) || $input['adaptExisting'] != -1) {
                // check if order with same model_id with status 'NEW' 1 is exist, then ask admin to adopt new order
                $order = Order::where('client_id', $client_id)->
                where('model_id', $input['model_id'])->
                where('status', 1)->
                first();

                if (isset($order) && !empty($order)) {
                    return Helper::success(self::SUCCESS, $order, 202);
                }
            }

            $input['po_number'] = "PO-" . \Carbon\Carbon::now();
            $order = Order::create($input);
        }

        $this->create_log($action, $client_id, $request, $order);

        return Helper::success(self::SUCCESS, $order, 200);
    }


    /**
     * Show a specific order
     * @param Order $order
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $client_id = (int)$request->client_id;
        $id = (int)$id;

        try {
            $order = Order::where('client_id', $client_id)->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Helper::fail(self::FAILED, []);
        }

        return Helper::success(self::SUCCESS, $order, 200);
    }


    /**
     * Get a specific order details by PO
     * @param Order $order
     * @return Response
     */

    public function bypo(Request $request)
    {
        $client_id = (int)$request->client_id;

        $rules = [
            'po' => 'required|max:100|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Helper::fail("Fail", $errors);
        }

        $order = Order::where('client_id', $client_id)
            ->where('po', $request->input('po'))
            ->first();

        return Helper::success(self::SUCCESS, $order);
    }


    /**
     * Update order information
     * @param Request $request
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $client_id = (int)$request->client_id;
        $id = (int)$id;

        $messages = array();
        $validator = Validator::make($request->all(), $this->rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Helper::fail("Fail", $errors);
        }

        try {
            $order = Order::where('client_id', $client_id)->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Helper::fail(self::FAILED, []);
        }

        $order->fill($request->all());
        $order->save();

        $this->create_log('update', $client_id, $request, $order);

        return Helper::success(self::SUCCESS, $order, 200);
    }

    /**
     * Update a field of an order information
     * @param Request $request
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateField($data, $id)
    {
        $client_id = (int)$data['client_id'];
        $id = (int)$id;

        try {
            $order = Order::where('client_id', $client_id)->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Helper::fail(self::FAILED, []);
        }

        $order->fill($data);
        $order->save();

//         $this->create_log('update', $client_id, $data, $order);

        return Helper::success(self::SUCCESS, $order, 200);
    }


    /**
     * Delete order information
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        \Log::debug($request->all());
        \Log::debug("indestroy menthod");
        $client_id = (int)$request->client_id;
        $id = (int)$id;

        try {
            $order = Order::where('client_id', $client_id)
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Helper::fail(self::FAILED, []);
        }

        // relationship deleting is handeled by the Order through boot method and 'deleting' event.
        $order->delete();

        $this->create_log('delete', $client_id, $request, $order);

        return Helper::success(self::SUCCESS, [], 200);
    }

    /**
     * Send orders to their respective suppliers
     * @param Request $request
     * @return Response
     */
    public function sendOrders(Request $request)
    {
        $client_id = (int)$request->client_id;
        $userId = (int)$request->user_id;
        $id = (int)$request->id;

        $input = $request->all();
        $date = \Carbon\Carbon::now(); // 2020-04-16 17:45:58
        $date = $date->format('Y-m-d H:i');


        if (isset($input['orders']) && !empty($input['orders'])) {
            $send_date = \Carbon\Carbon::now(); // 2020-04-16 17:45:58

            $order_data = [];
            $order_data['$REQUEST_NUMBER$'] = $request->request_number;
            $order_data['$USER$'] = $request->user;
            $order_data['$MANAGER$'] = $request->manager_name;
            $order_data['$REASON$'] = $request->reason;
            $order_data['$model_name$'] = $request->model_name;
            $order_data['$po_number$'] = $request->po_number;
            $order_data['$status$'] = $request->status;
            $order_data['$quantity$'] = $request->quantity;
            $order_data['$requested_quantity$'] = $request->requested_quantity;
            $order_data['$comment$'] = $request->comment;
            $order_data['$DATE$'] = $date;

            // Send an email to the user
            if (isset($input['user_email']) && !empty($input['user_email'])) {
                $order_table = '';
                if (view()->exists('sendOrderEmailToUser')) {
                    $order_table = view('sendOrderEmailToUser', ['orders' => $input['orders'], 'comments' => $input['comments']])->render();
                }
                $order_data = ['order_details' => $order_table];
                $order_data['preferable_lang_code'] = $request->preferable_lang_code;
                $order_data['$USER_EMAIL$'] = $input['user_email'];
                $this->dispatch_email_job($input, $input['user_email'], $order_data, 'SEND_PROCUREMENT_ORDER');
            }

            foreach ($input['orders'] as $supplier_id => $items) {
                foreach ($items as $basket_item) {
                    unset($data);
                    $data['client_id'] = $client_id;
                    $data['status'] = self::STATUS_SENT;
                    $data['comment'] = $input['comments'][$supplier_id];
                    $this->updateField($data, $basket_item['id']);
                }

                // Send an email to the supplier
                if (isset($items[0]['supplier_email']) && !empty($items[0]['supplier_email'])) {
                    $order_table = '';
                    if (view()->exists('sendOrderEmailToSupplier')) {
                        $order_table = view('sendOrderEmailToSupplier', ['orders' => $items, 'comments' => $input['comments'][$supplier_id]])->render();
                    }
                    $order_data = ['order_details' => $order_table];
                    $order_data['preferable_lang_code'] = $request->preferable_lang_code;
                    $order_data['$SUPPLIER_EMAIL$'] = $items[0]['supplier_email'];
                    $this->dispatch_email_job($input, $items[0]['supplier_email'], $order_data, 'SEND_PROCUREMENT_ORDER');
                }
            }
            $this->create_log('sent', $client_id, $request, $items[0]);
        }

        return Helper::success(self::SUCCESS, [], 200);
    }

    // sending email to user
    private function dispatch_email_job($input, $email, $order_data, $action)
    {
        // Send an email to the user
        if (isset($email) && !empty($email)) {
            // dispatching / queueing Email job
            //$action_date = \Carbon\Carbon::now(); // 2020-04-16 17:45:58
            //$action_date = $action_date->format('Y-m-d H:i');
            /*$data = [
                //'$ORDER_DETAILS$'   => $obj['order_details'],
                '$ACTION$'          => $action,
                '$DATE$'            => $action_date,
            ];*/
            $order_data['$ACTION$'] = $action;
            dispatch(new EmailJob($input['client_id'], $action, $order_data, $email));
        }
    }


    /**
     * Calling log service to create a log entry
     * @param string $action
     * @param integer $client_id
     * @param object $request
     * @param object $order
     */
    private function create_log($action, $client_id, $request, $order)
    {
        // create log entry here
        $result = $order ? self::SUCCESS : self::FAILED;
        $info = '';

        if (isset($this->LOG_SERVICE_URL) && !empty($this->LOG_SERVICE_URL)) {

            switch ($action) {
                case 'add':
                    $info = $order ? "Purchase order \"" . $order->po_number . "\" added." : 'Unable to add purchase order.';
                    break;

                case 'update':
                    $info = $order ? "Purchase order \"" . $order->po_number . "\" updated." : 'Unable to update purchase order.';
                    break;

                case 'delete':
                    $info = $order ? "Purchase order \"" . $order->po_number . "\" deleted." : 'Unable to delete purchase order.';
                    break;

                case 'sent':
                    $info = $order ? "Purchase order \"" . $order['po_number'] . "\" sent to the supplier." : 'Unable to send purchase order.';
                    break;

                default:
                    $info = 'Unknown action on purchase order';
                    break;
            }

            $headers = ['Clientid' => $client_id];
            $data = [
                'user' => $request->user ? $request->user : 'Unknown',
                'action' => $action,
                'target' => 'order',
                'info' => $info,
                'serialNumber' => '',
                'result' => $result,
                'type' => 'server',
            ];
            // dispatching / queueing logging job
            dispatch(new LoggingJob($this->LOG_SERVICE_URL, $headers, true, $data, 'POST', 'array'));
        }
    }
}
