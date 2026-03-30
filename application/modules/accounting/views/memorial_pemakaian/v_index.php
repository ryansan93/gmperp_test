<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="panel-heading no-padding">
					<ul class="nav nav-tabs nav-justified">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT MEMORIAL</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#action" data-tab="action">MEMORIAL</a>
						</li>
					</ul>
				</div>
				<div class="panel-body no-padding">
					<div class="tab-content">
						<div id="riwayat" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">

							<?php if ( $akses['a_submit'] == 1 ) { ?>
                                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                                    <button type="button" class="col-xs-12 btn btn-success pull-right" onclick="mm.add_data(this)" data-href="action" data-edit=""><i class="fa fa-plus"></i> ADD</button>
                                </div>

                                <div class="col-xs-12 no-padding"><hr></div>
                            <?php } ?>

                            <div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
                                <div class="col-xs-12 no-padding">
                                    <label class="control-label">Tgl Awal</label>
                                </div>
                                <div class="col-xs-12 no-padding">
                                    <div class="input-group date datetimepicker" name="startDate" id="StartDate">
                                        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $start_date; ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
                                <div class="col-xs-12 no-padding">
                                    <label class="control-label">Tgl Akhir</label>
                                </div>
                                <div class="col-xs-12 no-padding">
                                    <div class="input-group date datetimepicker" name="endDate" id="EndDate">
                                        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $end_date; ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 no-padding">
                                <button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="mm.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
                            </div>

                            <div class="col-xs-12 no-padding"><hr></div>

                            <div class="col-xs-12 no-padding">
                                <small>
                                    <table class="table table-bordered tbl_riwayat">
                                        <thead>
                                            <tr>
                                                <th class="col-xs-2">No. Memo Pemakaian</th>
                                                <th class="col-xs-2">Periode</th>
                                                <th class="col-xs-1">Keterangan</th>
                                                <th class="col-xs-1">Debet</th>
                                                <th class="col-xs-1">Kredit</th>
                                                <th class="col-xs-2">Nilai</th>
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

						<div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">
							
                        <?php if ( $akses['a_submit'] == 1 ) { ?>
                            <div class="col-xs-7 no-padding" style="padding-right: 5px;">
                                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                                    <div class="col-xs-3 no-padding"><label class="control-label">No. Memo</label></div>
                                    <div class="col-xs-4 no-padding">
                                        <input type="text" class="col-xs-12 form-control no_mm uppercase" placeholder="No. Memo" disabled>
                                    </div>
                                </div>
                               
                                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                                    <div class="col-xs-3 no-padding"><label class="control-label">Tanggal Memo</label></div>
                                    <div class="col-xs-4 no-padding">
                                        <div class="input-group date datetimepicker lock_date_fiskal" name="tglMm" id="TglMm">
                                            <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo date('Y-m-d'); ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                              
                               
                                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                                    <div class="col-xs-3 no-padding"><label class="control-label">Keterangan</label></div>
                                    <div class="col-xs-9 no-padding">
                                        <textarea class="form-control keterangan"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5 no-padding" style="padding-left: 5px;">
                                <div class="col-xs-12 no-padding hide" style="margin-bottom: 5px;">
                                    <div class="col-xs-3">&nbsp;</div>
                                    <div class="col-xs-3 no-padding"><label class="control-label">Total</label></div>
                                    <div class="col-xs-6 no-padding nilai">
                                        <input type="text" class="col-xs-12 form-control text-right nilai uppercase" placeholder="Total" disabled>
                                    </div>
                                </div>

                                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                                    <div class="col-xs-3">&nbsp;</div>
                                    <div class="col-xs-3 no-padding"><label class="control-label">Debet</label></div>
                                    <div class="col-xs-6 no-padding nilai">
                                        <input type="text" class="col-xs-12 form-control text-right tot_debet uppercase" placeholder="Total" disabled>
                                    </div>
                                </div>
                                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                                    <div class="col-xs-3">&nbsp;</div>
                                    <div class="col-xs-3 no-padding"><label class="control-label">Kredit</label></div>
                                    <div class="col-xs-6 no-padding nilai">
                                        <input type="text" class="col-xs-12 form-control text-right tot_kredit uppercase" placeholder="Total" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

                            <div class="col-xs-12 no-padding">
                                <div class="col-xs-12 no-padding" style="overflow-x: auto;">
                                    <small>
                                        <table class="table table-bordered tbl_detail" style="margin-bottom: 0px;  width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th style="width:150px;">Unit</th>
                                                    <th style="width:150px;">Plasma</th>
                                                    <th style="width:100px;">No. Reg</th>
                                                    <th style="width:100px;">Umur LHK</th>
                                                    <th style="width:150px;">Debet</th>
                                                    <th style="width:150px;">Kredit</th>
                                                    <th style="width:250px;">Keterangan</th>
                                                    <th style="width:150px;">Nilai</th>
                                                    <th style="width:150px; text-align:center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="data" data-urut="">
                                                    <td>
                                                        <select class="form-control unit" data-required="1">
                                                            <?php if ( !empty($unit) ): ?>
                                                                <?php foreach ($unit as $k_unit => $v_unit): ?>
                                                                    <option value="<?php echo $v_unit['kode']; ?>"><?php echo $v_unit['nama']; ?></option>
                                                                <?php endforeach ?>
                                                            <?php endif ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control plasma" data-required="1" onchange="mm.setDataNoreg(this, event)">
                                                            <?php if ( !empty($plasma) ): ?>
                                                                <option disabled selected>-- Pilih Plasma --</option>
                                                                <?php foreach ($plasma as $k_plasma => $v_plasma): ?>
                                                                    <option value="<?php echo $v_plasma['nim']; ?>"><?php echo $v_plasma['nama']; ?></option>
                                                                <?php endforeach ?>
                                                            <?php endif ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control noreg" data-required="1" onchange="mm.setUmurLhk(this, event)"> </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control umur-lhk" data-required="1" onchange=""> </select>
                                                    </td>
                                                    <td> 
                                                        <select class="form-control tujuan" >
                                                            <option value="">Pilih COA</option>
                                                            <?php if ( !empty($coa) ): ?>
                                                                <?php foreach ($coa as $k_coa => $v_coa): ?>
                                                                    <option value="<?php echo $v_coa['no_coa']; ?>" data-nama="<?php echo $v_coa['nama_coa']; ?>" ><?php echo $v_coa['no_coa'].' | '.$v_coa['nama_coa']; ?></option>
                                                                <?php endforeach ?>
                                                            <?php endif ?>
                                                        </select>
                                                    </td>
                                                    <td> 
                                                        <select class="form-control asal" >
                                                            <option value="">Pilih COA</option>
                                                            <?php if ( !empty($coa) ): ?>
                                                                <?php foreach ($coa as $k_coa => $v_coa): ?>
                                                                    <option value="<?php echo $v_coa['no_coa']; ?>" data-nama="<?php echo $v_coa['nama_coa']; ?>" ><?php echo $v_coa['no_coa'].' | '.$v_coa['nama_coa']; ?></option>
                                                                <?php endforeach ?>
                                                            <?php endif ?>
                                                        </select></td>
                                                    <td>
                                                        <textarea type="text"  style="width:100%;" class="form-default keterangan uppercase" placeholder="Keterangan" maxlength="50"></textarea>
                                                    </td>
                                            
                                                    <td>
                                                        <input type="text"  style="width:100%;" class="form-default text-right nilai uppercase" placeholder="Nilai" data-tipe="decimal" maxlength="19" data-required="1" onblur="mm.hitGrandTotal(this)">
                                                    </td>
                                                    <td style="text-align:center">                                                       
                                                        <button type="button" class=" btn btn-sm btn-danger" onclick="mm.removeRow(this)"><i class="fa fa-times"></i></button>                                                
                                                        <button type="button" class=" btn btn-sm btn-primary" onclick="mm.addRow(this)"><i class="fa fa-plus"></i></button>                                                                                    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </small>
                                </div>
                            </div>

                            <div class="col-xs-12 no-padding"><hr></div>

                            <div class="col-xs-12 no-padding">
                                <button type="button" class="btn btn-primary pull-right" onclick="mm.save()"><i class="fa fa-save"></i> Simpan</button>
                            </div>
                        <?php } else { ?>
                            <h4>MEMORIAL PEMAKAIAN</h4>
                        <?php } ?>


						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>