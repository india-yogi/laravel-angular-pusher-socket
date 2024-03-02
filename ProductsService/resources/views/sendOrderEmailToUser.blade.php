@foreach ($orders as $order)				            
	<div>
		<div>
	            <div class="col-md-12">
	            	<label for="supplier"><span translate>Supplier</span> : </label>
	            	<span>{{$order[0]['supplier']}}</span>
			    </div>
		
	            <div class="col-md-12">
	            	<label for="summary">
	            		<span translate>Order Summary</span> : 
	            	</label>
			        <table class="table table-hover table-bordered">
			            <thead>
			                <tr>
			                    <th class="col-sm-1">
			                    	<span translate>Model</span>
			                    </th>
			                    <th class="col-sm-1">
			                    	<span translate>Quantity</span>
			                    </th>
			                </tr>
			            </thead>
			            <tbody>
								@foreach ($order as $item)				            
					                <tr>
					                    <td>{{$item['model_name']}}</td>
					                    <td>{{$item['quantity']}}</td>
					                </tr>
								@endforeach
						</tbody>
			        </table>
			    </div>

	            <div class="col-md-12">
			        <div class="form-group">
			            <label for="model">
			            	<span translate>Comments</span> :
			           	</label>
			           	<div>{{$comments[$item['supplier_id']]}}</div>
			        </div>
			    </div>
		</div>
	</div> <!-- Outer Loop end -->        
@endforeach		
