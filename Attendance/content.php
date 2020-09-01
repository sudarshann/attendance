		<div id='wpbody' role='main'>
				<div id='wpbody-content'>
					<div class="container">
				<div class='row'>
					<div class='col-md-12'>
						<br>
  						<div id='calendar'></div>
					</div>
					<div>
						<div class='modal fade bd-example-modal-lg model_attadance' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
						  <div class='modal-dialog modal-lg'>
						    <div class='modal-content' style="height: 400px;z-index: 999999 !important;">

						     	<div id="sample2" class="ui-widget-content" style="padding: .5em;">
							<span style="display: none;" class="date_value"></span>
						     		<div class="container">
						     			<div class="row">
						     				<div class="col-md-3">
										        <?php
										         	$users = get_users();
												 	echo '
						     					 		<label>User List</label><br/>
   														 <select name="basic[]" multiple="multiple" class="3col active">
												 	';
													foreach ($users as $key => $value) {
														echo "<option value=".$value->id.">".$value->user_login."</optoin>";
													}
												 	echo "</select>";
												?>
						     				</div>
						     				<div class="col-md-3">
						     					 <label>Check In</label><br/>
							            		 <input class="checkin" autocomplete="off" name="s2Time2" value="" />
						     				</div>
						     				<div class="col-md-3">
						     					 <label>Check Out</label><br/>
							            		 <input class="checkout"  autocomplete="off" value="" />
						     				</div>
						     				<div class="col-md-3">
						     					<p>&nbsp;</p>
						     					<button class="button btn-success attandance_record">Submit</button>
						     				</div>
						     				<br>
						     				<br>
						     				<br>
						     				<div class="">
						   <table class="table tbl-border tbl-snipped att_table display "  width="100%" cellspacing="0">
							   	<thead>
									<tr>
										<th style="display: none;">SI</th>
										<th>Name</th>
										<th>Check in</th>
										<th>Check Out</th>
										<th>Date</th>
										<th>Delete</th>
										<th>Edit</th>
										<!-- <th>Edit</th>
										<th>Delete</th> -->
									</tr>							   		
							   	</thead>

							   
						   </table>
						</div>	
						     			</div>
						     		</div>
							   	</div>
						    </div>
						  </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Modal -->
		<div class="modal fade editmodel" id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		       	<form>
				      <div class="modal-body">
				       		<input type="text" class="form-control id_edit" style="display: none;" name="id">
				       		<label>Checkin</label>
				       		<input type="text" class="form-control checkin_edit" name="checkin">
				       		<label>Checkout</label>
				       		<input type="text" class="form-control checkout_edit" name="checkout">
				      </div>
				      <div class="modal-footer">
				        
				        <button type="submit" name="edit_submit" class="btn btn-primary">Save changes</button>
				      </div>
		       	</form>
		    </div>
		  </div>
		</div>	
		<span style="display: none" class="file_path"><?php echo get_template_directory_uri();?></span>