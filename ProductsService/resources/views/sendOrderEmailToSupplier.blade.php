<div>
	<div>
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
							@foreach ($orders as $order)				            
				                <tr>
				                    <td>{{$order['model_name']}}</td>
				                    <td>{{$order['quantity']}}</td>
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
		           	<div>{{$comments}}</div>
		        </div>
		    </div>
	</div>
</div> <!-- Outer Loop end -->        
