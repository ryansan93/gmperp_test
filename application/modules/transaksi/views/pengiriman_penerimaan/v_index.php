<div class="row content-panel detailed">
	<!-- <h4 class="mb">Rencana Chick In Mingguan</h4> -->
	<div class="col-lg-12 detailed">
		<input type="hidden" data-noreg="">
		<form role="form" class="form-horizontal">
			<div class="panel-heading">
				<ul class="nav nav-tabs nav-justified">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">Riwayat Pengiriman</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#pengiriman_pakan" data-tab="pengiriman_pakan">Pengiriman Pakan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#pengiriman_ovk" data-tab="pengiriman_ovk">Pengiriman OVK</a>
					</li>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div id="riwayat" class="tab-pane fade show active">
						<div class="col-lg-10 search no-padding d-flex align-items-center">
							<div class="col-sm-1 no-padding">
								<span> Periode </label>
							</div>
							<div class="col-sm-4">
								<div class="input-group date datetimepicker" name="startDate" id="StartDate">
							        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" />
							        <span class="input-group-addon">
							            <span class="glyphicon glyphicon-calendar"></span>
							        </span>
							    </div>
							</div>
							<div class="col-sm-1 text-center no-padding" style="max-width: 4%;">s/d</div>
							<div class="col-sm-4">
								<div class="input-group date datetimepicker" name="endDate" id="EndDate">
							        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" />
							        <span class="input-group-addon">
							            <span class="glyphicon glyphicon-calendar"></span>
							        </span>
							    </div>
							</div>
							<div class="col-sm-1 text-center no-padding" style="max-width: 4%;">Unit</div>
                            <div class="col-sm-2">
                                <select class="form-control unit">
                                    <!-- <option value="all">All</option> -->
                                    <?php if ( !empty($unit) ): ?>
                                        <?php foreach ($unit as $k_unit => $v_unit): ?>
                                            <option value="<?php echo $v_unit['kode']; ?>"><?php echo strtoupper($v_unit['nama']); ?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
							
						</div>
						<div class="col-lg-2 action no-padding">
							<div class="col-lg-12 search left-inner-addon no-padding pull-right" style="margin-left: 10px;">
								<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_pengiriman" placeholder="Search" onkeyup="filter_all(this)">
							</div>
						</div>
                        
                        <div>  
                            <span for="">Jenis Transaksi</span>                            
                            <select id="jenis_transaksi" style=" cursor:pointer; margin-left:20px; padding:6px; border-radius:5px; border:1px solid #ccc; box-shadow:inset 0 1px 1px rgba(0,0,0,.075)">
                                <option value="PAKAN">PAKAN</option>
                                <option value="OVK">OVK</option>
                            </select>
							<button id="btn-tampil" type="button" data-href="action" class="btn btn-primary cursor-p " title="TAMPIL" onclick="pp.get_lists()" style="margin-left: 10px;">Tampilkan</button>
							<button id="btn-add" type="button" data-href="pengiriman_pakan" class="btn btn-primary cursor-p " title="ADD" onclick="pp.changeTabActive(this)"><i class="fa fa-plus" aria-hidden="true"></i> ADD</button>
                        </div>
                        <br>
						<div style="height:300px; overflow:scroll;">
							<small>
								<table class="table table-bordered tbl_data" style="border-collapse: separate; border-spacing: 0;">
									<thead>
										<tr>
											<th class="col-sm-1 text-center" style="position: sticky; top: 0; z-index: 2;">No. Order</th>
											<th class="col-sm-1 text-center" style="position: sticky; top: 0; z-index: 2;">Tgl Kirim</th>
											<th class="col-sm-4 text-center" style="position: sticky; top: 0; z-index: 2;">Asal</th>
											<th class="col-sm-3 text-center" style="position: sticky; top: 0; z-index: 2;">Tujuan</th>
											<th class="col-sm-1 text-center" style="position: sticky; top: 0; z-index: 2;">No. Polisi</th>
											<th class="col-sm-1 text-center" style="position: sticky; top: 0; z-index: 2; white-space:nowrap">Tgl Terima</th>
											<th class="col-sm-1 text-center" style="position: sticky; top: 0; z-index: 2;"></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td colspan="7">Data tidak ditemukan.</td>
										</tr>
									</tbody>
								</table>
							</small>
						</div>
					</div>
					<div id="pengiriman_pakan" class="tab-pane fade">
						<?php echo $add_form_pakan; ?>
					</div>

					<div id="pengiriman_ovk" class="tab-pane fade">
						<?php echo $add_form_ovk; ?>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>