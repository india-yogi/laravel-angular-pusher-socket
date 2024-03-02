import {Component, OnInit, ViewChild} from '@angular/core';

import {EchoPusherService} from './services/echo-pusher.service';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// This lets me use jquery
declare var $: any;

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.css']
})
export class ProductsComponent implements OnInit {

  private pusherEcho!: Echo;
  private echo!: Echo;
  constructor(
    private echoPusherService: EchoPusherService,
  ) {

  }

  ngOnDestroy(): void {
    let channel: string = 'orders.'+this.client_id;
    this.echoPusherService.leaveChannel(channel);
  }

  private startListeningEchoEvents() {
    // #1 Start listening products listing event (it should be on a private channel)
    let channel: string = 'products.' + this.client_id;
    let eventToListen: string = 'ProductsFetched';

    this.echoPusherService.startListening(channel, eventToListen).subscribe(value => {
      console.log('Data from event in component...', value);
      this.populateOrdersData();
    });
  }

  ngOnInit() {
    // start listening all events
    this.startListeningEchoEvents();
  }

  // reset order search form, and get data
  resetOrders() {
    this.searchForm.reset();
    this.searchText = '';
    this.searchSupplier = 0;
    this.searchStatus = 0;

    this.pageNumber = 0; // brings back pagination to first page after every new search
    this.getOrders();
  }

  // Show an Order
  showOrder(id): void {
    this.errors = '';
    this.clearForm();

    // We don't needs to call API to get the order details, all details are already in 'this.orders' array
    // lets try this, if it will not work then we will revert back to API call ;)

    // this.facadeService.ordersService.showOrder(+id)
    // .subscribe(
    // 		(newOrder: any)	=>
    // 		{
    // 			this.order = newOrder.data;
    // 			this.orderForm.patchValue(newOrder.data);
    // 			this.getModelsByType();
    // 		}, err => {
    // 		}
    // );

    this.order = <any> this.orders.filter(function(order) {
      if (this == order.id) {
        return order;
      }
    }, id);

    if (this.order != null && this.order != undefined) {
      this.order = this.order[0];
      this.orderForm.patchValue(this.order);
      this.getModelsByType();
    }

    $('#orderModal').modal('show');
    // communication to show the modal, I use a behaviour subject from a service layer here
  }

  onSort(key) {
    if (this.sortKey === key) {
      if (this.sortDirection === 'asc') {
        this.sortDirection = 'desc';
      } else {
        this.sortDirection = 'asc';
      }
    }

    this.sortKey = key;
    this.pageNumber = 1;
    this.getOrders();
  }

  // Add all Local Users
  getOrders() {
    this.facadeService.ordersService.getOrders(
      this.pageNumber,
      this.searchText,
      this.searchSupplier,
      this.searchStatus,
      this.sortKey,
      this.sortDirection,
      this.page_limit
    ).subscribe((orders: any) => {
      // do something here
    });

    this.populateOrdersData();

    /*			this.facadeService.ordersService.getOrders(
          this.pageNumber,
          this.searchText,
          this.searchSupplier,
          this.searchStatus,
          this.sortKey,
          this.sortDirection,
          this.page_limit
        ).subscribe((orders:any)=>{
          if(orders){
            this.orders 		= 	orders.data;

            if(orders.paginator){
              this.totalItems		=	orders.paginator.total;
              this.itemsPerPage	=	orders.paginator.perPage;
              this.currentPage	=	orders.paginator.currentPage;
            }
          }
        });
    */
  }

  private populateOrdersData(){
    console.log('Event data in function...', this.orders_data);
    this.loaded = true;
    if (this.orders_data.orders) {
      this.orders = this.orders_data.orders.data;

      this.totalItems = this.orders_data.orders.total;
      this.itemsPerPage = this.orders_data.orders.per_page;
      this.currentPage = this.orders_data.orders.current_page;
    }else{
      this.orders = [];
      this.totalItems = 0;
      this.currentPage = 1;
    }
  }

  // Show add/creat order modal (popup)
  createOrder() {
    if (!this.writePermission) {
      return;
    }

    this.clearForm();
    this.order = null;

    if (this.types != null && this.types != undefined && this.types.length > 0) {
      this.orderForm.get('type_id').setValue(this.types[0].id);
      this.orderForm.get('type_id').updateValueAndValidity();

      this.getModelsByType();
    }

    if (this.models && this.models[0] != null && this.models[0] != undefined) {
      this.orderForm.get('model_id').setValue(this.models[0].id, {emitModelToViewChange: true});
      this.orderForm.get('model_id').updateValueAndValidity();

      this.orderForm.get('model_name').setValue(this.models[0].alias);
    }

    $('#orderModal').modal('show');
  }

  // Add an Order to the server (call API)
  addOrder(orderForm: any) {
    if (!this.writePermission) {
      return;
    }

    this.submitted = true;
    // this.clearForm();

    if (orderForm.value.id != null) { // updating existing order
      // disabling any validations
    }

    // stop here if form is invalid
    if (orderForm.invalid) {
      this.scrollModalToTop($('#orderModal'));
      return;
    }

    let user_id = this.facadeService.getUserProfile().id;
    orderForm.value.user_id = user_id;

    if (orderForm.value.id === null) { // adding new order
      this.facadeService.ordersService.storeOrder(<Order> orderForm.value)
        .subscribe(
          (newOrder: any) => {
            if (newOrder != null && newOrder.status == 202) {
              let msg = 'Are you sure you want to add this entry? ';
              msg = msg + 'You might want to adapt the existing pending order for this model???';

              if (confirm(msg)) {
                this.showOrder(newOrder.data.id);
              } else {
                orderForm.value.adaptExisting = -1;
                this.saveOrder(orderForm);
              } // dont' adapt
            } else if (newOrder != null && newOrder.status == 200) {
              this.getOrders();
              orderForm.reset();
              this.orderForm.reset();
              $('#orderModal').modal('toggle');
            } else if (newOrder.status == 500) {
              this.errors = [];
              this.errors.push(newOrder.msg);
            } else {
              this.errors = Object.keys(newOrder.data).map(key => (newOrder.data[key]));
            }

          }
        );
    } else {
      this.saveOrder(orderForm);
    }
  }

  // saving order data on server
  saveOrder(orderForm: any) {
    if (orderForm.value.id === null) { // adding new order

      this.facadeService.ordersService.storeOrder(<Order> orderForm.value)
        .subscribe(
          (newOrder: any) => {
            if (newOrder != null && newOrder.status == 200) {
              this.getOrders();
              orderForm.reset();
              this.orderForm.reset();
              $('#orderModal').modal('toggle');
            } else if (newOrder.status == 500) {
              this.errors = [];
              this.errors.push(newOrder.msg);
            } else {
              this.errors = Object.keys(newOrder.data).map(key => (newOrder.data[key]));
            }
          }, err => {
            this.errors = err.json().errors;
          }
        );
    } else { // updating existing user

      this.facadeService.ordersService.updateOrder(<Order> orderForm.value)
        .subscribe(
          (newOrder: any) => {
            if (newOrder.status == 200) {
              this.getOrders();
              orderForm.reset();
              this.orderForm.reset();
              $('#orderModal').modal('toggle');
            } else if (newOrder.status == 500) {
              this.errors = [];
              this.errors.push(newOrder.msg);
            } else {
              this.errors = Object.keys(newOrder.data).map(key => (newOrder.data[key]));
            }
          }, err => {
            this.errors = err.json().errors;
          }
        );
    }
  }

  // Add a Order
  deleteOrder(id, name = '') {
    if (confirm(this.translate.instant('messages.warnings.order_delete'))) {

      this.facadeService.ordersService.deleteOrder(+id)
        .subscribe((res: any) => {
            if (res.data != null && res.data != '' && res.data != undefined) {
              alert(res.data);
            }
            console.log('Order deleted and refreshed table');
            this.getOrders();
          }, err => {
          }
        );
    }
  }

  // getting models of a type
  getModelsByType() {
    let type = this.orderForm.value.type_id;

    // getting all models
    this.facadeService.modelsService.getModelsByType(type).subscribe((models: any) => {
      if (models && models != null && models.status == 200) {
        this.models = [];
        this.models = models.data;
        if (this.order != null && this.order != undefined) {
          this.orderForm.get('model_id').setValue(this.order.model_id, {emitModelToViewChange: true});
          this.orderForm.get('model_id').updateValueAndValidity();
        } else {
          if (this.models && this.models[0] != null && this.models[0] != undefined) {
            // this.orderForm.controls.model_id.patchValue(this.models[0].id);
            this.orderForm.get('model_id').setValue(this.models[0].id, {emitModelToViewChange: true});
            this.orderForm.get('model_id').updateValueAndValidity();

            this.orderForm.get('model_name').setValue(this.models[0].alias);

            this.default_model_supplier_id = this.models[0].supplier_id ? this.models[0].supplier_id : 0;

            this.orderForm.get('supplier_id').setValue(this.default_model_supplier_id, {emitModelToViewChange: true});
            this.orderForm.get('supplier_id').updateValueAndValidity();
          }
        }

      } else {
        this.orderForm.get('model_id').setValue(null);
        this.orderForm.get('model_id').updateValueAndValidity();
        this.models = [];
      }

    });
  }

  setModelName(event) {
    this.orderForm.get('model_name').setValue(event.target.selectedOptions[0].label);
    this.orderForm.get('model_name').updateValueAndValidity();
    let tmp_supplier_id = 0;

    // getting the default supplier of the selected model and setting that in the suppliers select
    // list
    if (this.models.length > 0) {
      this.models.forEach(function select(mod) {
        if (mod.supplier_id != null && event.target.value == mod.id) {
          tmp_supplier_id = mod.supplier_id;
        }
      });
    }

    this.orderForm.get('supplier_id').setValue(tmp_supplier_id, {emitModelToViewChange: true});
    this.orderForm.get('supplier_id').updateValueAndValidity();
  }

  //Select all checkboxes/orders from orders list
  selectAllOrders(event) {
    if (event.target.checked == true) {
      this.selectedOrders = [];
      this.ordersToSent = [];

      this.orders.forEach(function names(item) {
        if ((item.status == 'NEW' || item.status == '1') && item.supplier != undefined) {
          this.push(item.id);
        }
      }, this.selectedOrders);

      this.orders.forEach(function names(item) {
        if ((item.status == 'NEW' || item.status == '1') && item.supplier != undefined) {
          if (this[item.supplier_id] == null || this[item.supplier_id] == undefined) {
            this[item.supplier_id] = [];
          }

          this[item.supplier_id].push(item);
        }

        //this.push(item);
      }, this.ordersToSent);
    }

    if (event.target.checked == false) {
      this.selectedOrders = [];
      this.ordersToSent = [];
    }

  }

  //Select a single checkbox/order from order list and insert that into ordersToSent array
  selectOrder(event) {
    let sa = +event.target.value;

    if (event.target.checked == true) {
      this.selectedOrders.push(sa);

      this.orders.forEach(function names(item) {
        if (sa == item.id) {
          if (this[item.supplier_id] == null || this[item.supplier_id] == undefined) {
            this[item.supplier_id] = [];
          }
          this[item.supplier_id].push(item);
        }
      }, this.ordersToSent);
    }

    if (event.target.checked == false) {
      $('#all_checkboxes').prop('checked', false);

      this.selectedOrders = this.selectedOrders.filter(function(item) {
        return event.target.value != item;
      }, event);

      /**
       Getting the id of item which is unchecked, then finding the index of that id
       in ordersToSent array and removing that item.
       */
        // finding index in ordersToSent array

        // let tmpArray = this.ordersToSent.filter(function rem(ord, index){
        // 		return this != ord.id;
        // }, event.target.value);

        // this.ordersToSent = tmpArray;
        // tmpArray = [];

      let tmpArray = [];
      this.ordersToSent.forEach(function remove(items, index) {
        if (items != undefined && items != null && items.length > 0) {
          let tmp_orders = items.filter(function rem(ord, index) {
            return this != ord.id;
          }, this);
          tmpArray[index] = tmp_orders;
        }
      }, event.target.value);

      this.ordersToSent = tmpArray;
    }
  }

  // displaying send order form, with order summary
  sendOrder() {
    this.clearForm();
    $('#sendOrderModal').modal('show');
  }

  // Sending selected orders to suppliers
  sendOrderSubmit(form: NgForm) {
    form.value.orders = this.ordersToSent;
    let user_email = this.facadeService.getUserProfile().email;
    form.value.user_email = user_email;


    // if(form.value.orders.length > 0){
    // 	form.value.orders.forEach(function removeEmpty(item, index){
    // 		if(item.length == 0 || item == null){
    // 			console.log('Empty');
    // 			form.value.orders.splice(index); // removes an item from the array
    // 		}
    // 	});
    // }

    // sending selected orders to their respective suppliers
    this.facadeService.ordersService.sendOrders(form.value)
      .subscribe(
        (result: any) => {
          if (result != null && result.status == 200) {
            this.getOrders();
            this.orderForm.reset();

            this.ordersToSent = [];

            $('#sendOrderModal').modal('toggle');
            this.toastr.success(this.translate.instant('messages.order_success'));

            this.selectedOrders = [];
          } else if (result.status == 500) {
            this.errors = [];
            this.errors.push(result.msg);
            this.ordersToSent = [];
            this.getOrders();

            this.toastr.error(this.errors);
            this.selectedOrders = [];
          } else {
            this.errors = Object.keys(result.data).map(key => (result.data[key]));
            this.toastr.error(this.errors);
            this.selectedOrders = [];
          }
        }, err => {
          this.errors = err.json().errors;
          this.toastr.error(this.errors);
          this.selectedOrders = [];
        }
      );

    //$("#sendOrderModal").modal('toggle');
  }

  scrollModalToTop(modal) {
    modal.animate({scrollTop: 0}, 500);
  }

  pageChanged(event: any): void {
    this.pageNumber = event.page;
    this.getOrders();
  }

  // setting dynamic pagination limit and loading records
  setPageLimit(event) {
    this.page_limit = event.target.value;
    this.getOrders();
  }

}
