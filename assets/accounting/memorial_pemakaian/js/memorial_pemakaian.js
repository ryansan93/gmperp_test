var mm = {
	start_up: function () {
		mm.setting_up();

        if ( !empty($("#StartDate").find('input').data('tgl')) && empty($("#StartDate").find('input').val()) ) {
            var tgl = $("#StartDate").find('input').data('tgl');
            $("#StartDate").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }
        if ( !empty($("#EndDate").find('input').data('tgl')) && empty($("#EndDate").find('input').val()) ) {
            var tgl = $("#EndDate").find('input').data('tgl');
            $("#EndDate").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }
        mm.getLists();
	}, // end - start_up

	setting_up: function() {
        var today = moment(new Date()).format('YYYY-MM-DD');
        // $("#StartDate, #EndDate").datetimepicker({
        //     locale: 'id',
        //     format: 'DD MMM Y',
        //     defaultDate: new Date()
        // });

        // $("#StartDate").on("dp.change", function (e) {
        //     var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
        //     $("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
        // });
        // $("#EndDate").on("dp.change", function (e) {
        //     var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
        //     if ( maxDate >= (today+' 00:00:00') ) {
        //         $("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
        //     }
        // });


        $("#StartDate, #EndDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM YYYY'
        });

        let startOfMonth = moment().startOf('month');
        let endOfMonth   = moment().endOf('month');

        $("#StartDate").data("DateTimePicker").date(startOfMonth);
        $("#EndDate").data("DateTimePicker").date(endOfMonth);

        $("#StartDate").on("dp.change", function (e) {
            let start = e.date ? e.date.clone().startOf('day') : false;
            if (start) {
                $("#EndDate").data("DateTimePicker").minDate(start);
            }
        });

        $("#EndDate").on("dp.change", function (e) {
            let end = e.date ? e.date.clone().endOf('day') : false;
            if (end) {
                $("#StartDate").data("DateTimePicker").maxDate(end);
            }
        });

        $("#TglMm").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            defaultDate: new Date()
        });


      $.map($("#TglMm"), function(div) {
            let input = $(div).find('input');
            let tgl = input.data('tgl');

            if (tgl) {
                $(div).data('DateTimePicker').date(
                    moment(tgl, 'YYYY-MM-DD', true)
                );
            }
        });
        App.setTutupBulan();


        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal],[data-tipe=decimal3],[data-tipe=decimal4],[data-tipe=number]').each(function(){
            $(this).priceFormat(Config[$(this).data('tipe')]);
        });

        $('select.unit').select2();
        $('select.plasma').select2();
        $('select.noreg').select2();
        $('select.umur-lhk').select2();
        $('select.tujuan').select2();
        $('select.asal').select2();
        

    }, // end - setting_up

    getDetJurnalTrans: function() {
        var jt_id = $('select.jurnal_trans').find('option:selected').attr('data-id');

        $.map( $('select.det_jurnal_trans'), function(select) {
            $(select).find('option').removeAttr('disabled');
            $(select).find('option:not([data-idjt="'+jt_id+'"])').attr('disabled', 'disabled');
            $(select).find('option[value="all"]').removeAttr('disabled');
            $(select).find('option[value=""]').removeAttr('disabled');
    
            $(select).select2().on("select2:select", function (e) {
                mm.getAsalTujuanCoa( $(select) );
            });

            mm.getAsalTujuanCoa( $(select) );
        });
    }, // end - getDetJurnalTrans

    getAsalTujuanCoa: function(elm) {
        var tr = $(elm).closest('tr');

        $(tr).find('select.asal').select2();
        $(tr).find('select.tujuan').select2();
        $(tr).find('select.unit').select2();

        var val_det_jurnal_trans = $(tr).find('select.det_jurnal_trans').select2().val();

        if ( !empty(val_det_jurnal_trans) ) {
            $(tr).find('select.asal').attr('disabled', 'disabled');
            $(tr).find('select.tujuan').attr('disabled', 'disabled');

            var asal = $(tr).find('select.det_jurnal_trans option:selected').attr('data-coaasal');
            var tujuan = $(tr).find('select.det_jurnal_trans option:selected').attr('data-coatujuan');

            $(tr).find('select.asal').select2().val( asal );
            $(tr).find('select.asal').trigger('change');
            $(tr).find('select.tujuan').select2().val( tujuan );
            $(tr).find('select.tujuan').trigger('change');
        } else {
            $(tr).find('select.asal').removeAttr('disabled', 'disabled');
            $(tr).find('select.tujuan').removeAttr('disabled', 'disabled');
        }
    }, // end - getTujuanCoa
   
 
    addRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        var tr_clone = $(tr).clone();

        $(tr_clone).find('select.unit, select.plasma, select.noreg, select.umur-lhk, select.asal, select.tujuan')
            .removeAttr('data-select2-id')
            .removeAttr('aria-hidden')
            .removeAttr('tabindex')
            .next('.select2').remove();

        $(tr_clone).find('select option').removeAttr('data-select2-id');


        $(tr_clone).find('input, select').val('');

        $(tr_clone).find('[data-tipe]').each(function(){
            $(this).priceFormat(Config[$(this).data('tipe')]);
        });

        $(tbody).append(tr_clone);
        $(tr_clone).find('select.unit, select.plasma, select.noreg, select.umur-lhk, select.asal, select.tujuan').select2();
    },

    removeRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 1 ) {
            $(tr).remove();

            mm.hitGrandTotal( $(tbody).find('tr:first()') );
        }
    }, // end - addRow

    hitGrandTotal: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        var grand_total = 0;
        var grand_total_debet = 0;
        var grand_total_kredit = 0;
        
        $.map( $(tbody).find('tr'), function (tr) {
            var ipt = $(tr).find('input.nilai');
            var nilai = parseFloat(numeral.unformat( $(ipt).val() ));

            var coa_asal = $(tr).find('select.asal').select2().val();
            var coa_tujuan = $(tr).find('select.tujuan').select2().val();
            if ( !empty(coa_asal) ) {
                grand_total_kredit += nilai;
            }
            if ( !empty(coa_tujuan) ) {
                grand_total_debet += nilai;
            }

            grand_total += nilai;
        });

        $('div.nilai input.nilai').val( numeral.format(grand_total) );
        $('div.nilai input.tot_debet').val( numeral.format(grand_total_debet) );
        $('div.nilai input.tot_kredit').val( numeral.format(grand_total_kredit) );
    }, // end - hitGrandTotal

	// changeTabActive: function(elm) {
    //     var vhref = $(elm).data('href');
    //     // var edit = $(elm).data('edit');
    //     // change tab-menu
    //     $('.nav-tabs').find('a').removeClass('active');
    //     $('.nav-tabs').find('a').removeClass('show');
    //     $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
    //     $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

    //     // change tab-content
    //     $('.tab-pane').removeClass('show');
    //     $('.tab-pane').removeClass('active');
    //     $('div#'+vhref).addClass('show');
    //     $('div#'+vhref).addClass('active');

    //     // if ( vhref == 'action' ) {
    //     //     var v_id = $(elm).attr('data-kode');

    //     //     mm.loadForm(v_id, edit);
    //     // };
    // }, // end - changeTabActive

    add_data : (elm) =>{

        var vhref = $(elm).data('href');
        
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        $.ajax({
            url: 'accounting/MemorialPemakaian/add_data',
            dataType: 'html',
            type: 'post',
            beforeSend: function() {
                showLoading('Proses load view . . .');
            },
            success: function(html) {
                hideLoading();
                let load  = $(".tab-detail").html(html);

                if (load){
                    mm.start_up();
                }
            },
        });

    },

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'accounting/MemorialPemakaian/loadForm',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                hideLoading();
                $(dcontent).html(html);
                mm.setting_up();

                // if ( !empty(resubmit) ) {
                //     var kode_cust = $(dcontent).find('select.customer').select2().val();
                //     if ( !empty(kode_cust) ) {
                //         mm.getNoFaktur( kode_cust );
                //     }

                //     var kode_supl = $(dcontent).find('select.supplier').select2().val();
                //     if ( !empty(kode_supl) ) {
                //         mm.getNoLpb( kode_supl );
                //     }
                // }
            },
        });
    }, // end - loadForm

	getLists: function() {
        var dcontent = $('div#riwayat');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else {
            var tbody = $(dcontent).find('table.tbl_riwayat tbody');

            var params = {
                'start_date': dateSQL( $(dcontent).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() )
            };

            // if ($.fn.dataTable.isDataTable('.tbl_riwayat')) {
            //     $('.tbl_riwayat').DataTable().destroy();
            // }

            $.ajax({
                url : 'accounting/MemorialPemakaian/getLists',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ App.showLoaderInContent( $(tbody) ); },
                success : function(html){
                    App.hideLoaderInContent( $(tbody), html );

                    // if ( $('.tbl_riwayat').find('tbody tr.data').length > 0 ) {
                    //     $('.tbl_riwayat').DataTable();
                    // }
                },
            });
        }
    }, // end - getLists


    cekData: function() {
        var dcontent = $('#action');
        var err = 0;
		$.map( $(dcontent).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

        console.log(err)

        var return_status = 0;
        var return_keterangan_error = null;

		if ( err > 0 ) {
			return_keterangan_error = 'Harap lengkapi data terlebih dahulu.';
		} else {
            var tot_debet = numeral.unformat($(dcontent).find('input.tot_debet').val());
            var tot_kredit = numeral.unformat($(dcontent).find('input.tot_kredit').val());

            if ( tot_debet == tot_kredit ) {
                return_status = 1;
    
                var keterangan_faktur = null;
                var keterangan_lpb = null;
                var tgl_faktur = null;
                var nilai_faktur = null;
                var tgl_lpb = null;
                var nilai_lpb = null;
                var debet = null;
                var kredit = null;
    
                var err_coa = 0;
                var keterangan_error = null;
                var tanggal = dateSQL( $(dcontent).find('#TglMm').data('DateTimePicker').date() );
                $.map( $(dcontent).find('.tbl_detail tbody tr'), function(tr) {
                    var coa_asal = $(tr).find('select.asal').select2().val();
                    var coa_tujuan = $(tr).find('select.tujuan').select2().val();

                    $(tr).find('select.asal').removeClass('has-error');
                    $(tr).find('select.tujuan').removeClass('has-error');
                    if ( empty(coa_asal) && empty(coa_tujuan) ) {
                        $(tr).find('select.asal').addClass('has-error');
                        $(tr).find('select.tujuan').addClass('has-error');

                        err_coa++;
                    }

                    var no_faktur = $(tr).find('select.faktur').select2().val();
                    var no_lpb = $(tr).find('select.lpb').select2().val();
                    if ( !empty(no_faktur) || !empty(no_lpb) ) {
                        keterangan_faktur = $(tr).find('select.faktur option:selected').text();
                        keterangan_lpb = $(tr).find('select.lpb option:selected').text();
    
                        tgl_faktur = $(tr).find('select.faktur option:selected').attr('data-tglfaktur');
                        nilai_faktur = $(tr).find('select.faktur option:selected').attr('data-nilai');
    
                        tgl_lpb = $(tr).find('select.lpb option:selected').attr('data-tgllpb');
                        nilai_lpb = $(tr).find('select.lpb option:selected').attr('data-nilai');
    
                        debet = numeral.unformat($(tr).find('input.debet').val());
                        kredit = numeral.unformat($(tr).find('input.kredit').val());

                        var nilai = (debet > 0) ? debet : kredit;
    
                        /* CEK TANGGAL FAKTUR */
                        if ( tanggal < tgl_faktur ) {
                            if ( empty( keterangan_error ) ) {
                                keterangan_error = 'Tanggal jurnal memorial lebih kecil dari tanggal faktur, cek kembali data yang anda masukkan.';
                            } else {
                                keterangan_error += '<br><br>Tanggal jurnal memorial lebih kecil dari tanggal faktur, cek kembali data yang anda masukkan.';
                            }
                            keterangan_error += '<br><b>'+keterangan_faktur+'</b>';
                        }
    
                        /* CEK TANGGAL LPB */
                        if ( tanggal < tgl_lpb ) {
                            if ( empty( keterangan_error ) ) {
                                keterangan_error = 'Tanggal jurnal memorial lebih kecil dari tanggal pembelian, cek kembali data yang anda masukkan.';
                            } else {
                                keterangan_error += '<br><br>Tanggal jurnal memorial lebih kecil dari tanggal pembelian, cek kembali data yang anda masukkan.';
                            }
                            keterangan_error += '<br><b>'+keterangan_lpb+'</b>';
                        }
    
                        /* CEK NOMINAL FAKTUR */
                        if ( nilai_faktur < nilai ) {
                            if ( empty( keterangan_error ) ) {
                                keterangan_error = 'Nominal bayar lebih besar dari nilai faktur, cek kembali data yang anda masukkan.';
                            } else {
                                keterangan_error += '<br><br>Nominal bayar lebih besar dari nilai faktur, cek kembali data yang anda masukkan.';
                            }
                            keterangan_error += '<br><b>'+keterangan_faktur+'</b>';
                        }
    
                        /* CEK NOMINAL LPB */
                        if ( nilai_lpb < nilai ) {
                            if ( empty( keterangan_error ) ) {
                                keterangan_error = 'Nominal bayar lebih besar dari nilai pembelian, cek kembali data yang anda masukkan.';
                            } else {
                                keterangan_error += '<br><br>Nominal bayar lebih besar dari nilai pembelian, cek kembali data yang anda masukkan.';
                            }
                            keterangan_error += '<br><b>'+keterangan_lpb+'</b>';
                        }
                    }
                });
    
                if ( err_coa > 0 ) {
                    return_keterangan_error = 'Ada data coa yang belum di isi, harap cek kembali data anda.';

                    return_status = 0;
                } else {
                    if ( !empty(keterangan_error) ) {
                        return_keterangan_error = keterangan_error;
                        return_keterangan_error += '<br><br>Apakah anda yakin ingin tetap menyimpan data jurnal memorial ?';
                    } else {
                        return_keterangan_error = 'Apakah anda yakin ingin menyimpan data jurnal memorial ?';
                    }
                }
            } else {
                return_keterangan_error = 'Data tidak balance, harap cek kembali.';
            }
        }

        return {'status': return_status, 'keterangan': return_keterangan_error}
    }, // end - cekData

	save: function() {
		var dcontent = $('#action');

        var cek_data = mm.cekData();
        var status = cek_data.status;
        var keterangan = cek_data.keterangan;

        if ( status == 0 ) {
			bootbox.alert( keterangan );
		} else {
			bootbox.confirm( keterangan , function(result) {
                if ( result ) {
                    showLoading('Proses simpan data memorial . . .');

                    var no_urut = 1;
                    var detail = $.map( $(dcontent).find('.tbl_detail tbody tr'), function(tr) {
                        var _detail = {
                            'coa_asal'          : $(tr).find('select.asal').select2().val(),
                            'coa_asal_nama'     : $(tr).find('select.asal option:selected').attr('data-nama'),
                            'coa_tujuan'        : $(tr).find('select.tujuan').select2().val(),
                            'coa_tujuan_nama'   : $(tr).find('select.tujuan option:selected').attr('data-nama'),
                            'keterangan'        : $(tr).find('textarea.keterangan').val().toUpperCase(),
							'nilai'             : numeral.unformat($(tr).find('input.nilai').val()),
                            'unit'              : $(tr).find('select.unit').select2().val(),
                            'plasma'            : $(tr).find('select.plasma').select2().val(),
                            'noreg'             : $(tr).find('select.noreg').select2().val(),
                            'umur_lhk'          : $(tr).find('select.umur-lhk').select2().val(),
                            'id_lhk'            : $(tr).find('select.umur-lhk option:selected').attr("id_lhk"),
                        };

                        no_urut++;

                        return _detail;
                    });

                    var data = {
                        'tgl_mm'        : dateSQL( $(dcontent).find('#TglMm').data('DateTimePicker').date() ),
                        'keterangan'    : $(dcontent).find('textarea.keterangan').val().trim().toUpperCase(),
                        'nilai'         : numeral.unformat($(dcontent).find('div.nilai input').val()),
                        'detail'        : detail
                    };

                    $.ajax({
                        url: 'accounting/MemorialPemakaian/save',
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            'params': data
                        },
                        beforeSend: function() {},
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                bootbox.alert( data.message, function () {
                                    // mm.loadForm( data.content.id );
                                    window.location.reload();
                                });
                            } else {
                                bootbox.alert(data.message);
                            };
                        },
                    });
                }
            });
        }
	}, // end - save

    edit: function(elm) {
        var dcontent = $('#action');

        var cek_data    = mm.cekData();
        var status      = cek_data.status;
        var keterangan  = cek_data.keterangan;

       

        if ( status == 0 ) {
			bootbox.alert( keterangan );
		} else {
			bootbox.confirm( keterangan , function(result) {
                   

                if ( result ) {
                    // showLoading('Proses simpan data . . .');

                    var no_urut = 1;
                     var detail = $.map( $(dcontent).find('.tbl_detail tbody tr'), function(tr) {
                        var _detail = {
                            'coa_asal'          : $(tr).find('select.asal').select2().val(),
                            'coa_asal_nama'     : $(tr).find('select.asal option:selected').attr('data-nama'),
                            'coa_tujuan'        : $(tr).find('select.tujuan').select2().val(),
                            'coa_tujuan_nama'   : $(tr).find('select.tujuan option:selected').attr('data-nama'),
                            'keterangan'        : $(tr).find('textarea.keterangan').val().toUpperCase().trim(),
							'nilai'             : numeral.unformat($(tr).find('input.nilai').val()),
                            'unit'              : $(tr).find('select.unit').select2().val(),
                            'plasma'            : $(tr).find('select.plasma').select2().val(),
                            'noreg'             : $(tr).find('select.noreg').select2().val(),
                            'umur_lhk'          : $(tr).find('select.umur-lhk').select2().val(),
                            'id_lhk'            : $(tr).find('select.umur-lhk option:selected').attr("id_lhk"),
                        };

                        no_urut++;

                        return _detail;
                    });


                    var data = {
                        'no_mmpem'      : $(".no_mmpem").val(),
                        'tgl_mm'        : dateSQL( $(dcontent).find('#TglMm').data('DateTimePicker').date() ),
                        'keterangan'    : $(dcontent).find('textarea.keterangan_hdr').val().trim().toUpperCase(),
                        'nilai'         : numeral.unformat($(dcontent).find('div.nilai input').val()),
                        'detail'        : detail
                    };

               
                    $.ajax({
                        url: 'accounting/MemorialPemakaian/edit',
                        dataType: 'json',
                        type: 'post',
                        data: {
                            'params': data
                        },
                        beforeSend: function() {},
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                bootbox.alert( data.message, function () {
                                    // mm.loadForm( data.content.id );
                                    window.location.reload();
                                });
                            } else {
                                bootbox.alert(data.message);
                            };
                        },
                    });
                }
            });
        }
    }, // end - edit

    delete: function(elm, e) {

        e.preventDefault();
        var dcontent = $('#action');

        bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
            if ( result ) {
                showLoading();

                var params = {
                    'no_mmpem': $(elm).attr('no_mmpem')
                };

                $.ajax({
                    url: 'accounting/MemorialPemakaian/delete',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'params': params
                    },
                    beforeSend: function() {},
                    success: function(data) {
                        hideLoading();
                        if ( data.status == 1 ) {
                            bootbox.alert( data.message, function () {
                                // mm.getLists();
                                // mm.loadForm();

                                window.location.reload();
                            });
                        } else {
                            bootbox.alert(data.message);
                        };
                    },
                });
            }
        });
    }, // end - delete

    printPreview: function (elm) {
        var no_mmpem = $(elm).attr('no_mmpem');

        window.open('accounting/MemorialPemakaian/printPreview/'+no_mmpem, 'blank');
    }, // end - printPreview

    exportPdf : function (elm) {
        var kode = $(elm).attr('data-kode');

        var params = {
            'kode': kode
        };

        $.ajax({
            url: 'accounting/MemorialPemakaian/exportPdf',
            dataType: 'json',
            type: 'post',
            data: {
                'params': params
            },
            beforeSend: function() {
                showLoading('Proses Print . . .');
            },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    // if ( $('iframe').length > 0 ) {
                    //     $('iframe').remove();
                    // }

                    // var ifr = document.createElement("iframe");
                    // ifr.src = data.content.url;
                    // ifr.id = "PDF";
                    // ifr.style.width = "0px";
                    // ifr.style.height = "0px";
                    // ifr.style.border = "0px";
                    // document.body.appendChild(ifr);

                    // var PDFG = document.getElementById("PDF");
                    // PDFG.contentWindow.print();
                } else {
                    bootbox.alert(data.message);
                };
            },
        });
    }, // end - exportPdf


    setDataNoreg: (elm,e) => {

        let $row = $(elm).closest("tr");

        let params = {
            nomor : $(elm).find("option:selected").val()
        };

        $.ajax({
            url: 'accounting/MemorialPemakaian/setDataNoreg',
            dataType: 'json',
            type: 'post',
            data:  params,
            beforeSend: function() {
                showLoading('Proses load data . . .');
            },
            success: function(data) {
                hideLoading();

                let $select = $row.find('.noreg');

                let option = `<option disabled selected>-- Pilih No. Reg --</option>`;

                if (Array.isArray(data)) {
                    data.forEach(function(item){
                        option += `<option value="${item.noreg}">${item.noreg}</option>`;
                    });
                }

                $select.html(option);

                let selected = $row.find('.noreg-selected').text().trim();

                // console.log('auto select noreg:', selected);

                if (selected) {
                    $select.val(selected).trigger('change');
                }
            }
        });
    },

    setUmurLhk: (elm ,e) => {

        let $row = $(elm).closest("tr");

        let params = {
            noreg : $(elm).find("option:selected").val()
        };

        $.ajax({
            url: 'accounting/MemorialPemakaian/setUmurLhk',
            dataType: 'json',
            type: 'post',
            data:  params,
            beforeSend: function() {
                showLoading('Proses load data . . .');
            },
            success: function(data) {
                hideLoading();

                let $select = $row.find('.umur-lhk');

                let option = `<option disabled selected>-- Pilih Umur LHK --</option>`;

                data.forEach(function(item){
                    option += `<option id_lhk="${item.id}" value="${item.umur}">${item.umur}</option>`;
                });

         
                $select.html(option);
                let selected = $row.find('.umur-lhk-selected').text()?.trim();
                // console.log('auto select umur:', selected);
                if (selected) {
                    $select.val(selected).trigger('change');
                }
            }
        });
    },

    viewDetail: (elm, e) => {
        var vhref = $(elm).data('href');
   
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');
        
        let params = {
            no_mmpem : $(elm).attr("no_mmpem"),
            keterangan : $(elm).attr("keterangan"),
            debet : $(elm).attr("debet"),
            kredit : $(elm).attr("kredit"),
        }
        console.log(params)
          
        $.ajax({
            url: 'accounting/MemorialPemakaian/showDetailMemoPemakaian',
            dataType: 'html',
            type: 'post',
            data:  params,
            beforeSend: function() {
                showLoading('Proses load data . . .');
            },
            success: function(html) {
                hideLoading();
                $(".tab-detail").html(html)

                // $(".header_data").attr("no_mmpem", params.no_mmpem);
                $(".btn-edit").attr("keterangan", params.keterangan);
            },
        });
    },

    backDetail: (elm, e) => {
        
        let params = {
            no_mmpem : $(elm).attr("no_mmpem"),
            keterangan : $(elm).attr("keterangan"),
            debet : $(elm).attr("debet"),
            kredit : $(elm).attr("kredit"),
        }
        console.log(params)
          
        $.ajax({
            url: 'accounting/MemorialPemakaian/showDetailMemoPemakaian',
            dataType: 'html',
            type: 'post',
            data:  params,
            beforeSend: function() {
                showLoading('Proses refresh data . . .');
            },
            success: function(html) {
                
             
                let load = $(".tab-detail").html(html)

                if(load){
                    $(".btn-edit").attr("keterangan", params.keterangan);

                    setTimeout(function(){
                        hideLoading();
                    },1000)
                }

                // $(".header_data").attr("no_mmpem", params.no_mmpem);
            },
        });
    },

    edit_data: (elm, e) => {

        e.preventDefault();
        
        let params = {
            no_mmpem : $(elm).attr("no_mmpem"),
            keterangan : $(elm).attr("keterangan"),
        }
          
        $.ajax({
            url: 'accounting/MemorialPemakaian/edit_data',
            dataType: 'html',
            type: 'post',
            data:  params,
            beforeSend: function() {
                showLoading('Proses load data . . .');
            },
            success: function(html) {
                hideLoading();
                let load  = $(".tab-detail").html(html)

                if (load ){
                    mm.start_up();
                    mm.setPlasmaSelected();

                    $(".no_mmpem").val(params.no_mmpem)
                    $(".keterangan_hdr").val(params.keterangan)

                    
                    $(".btn-edit").attr("no_mmpem", params.no_mmpem);
                    $(".btn-batal").attr("keterangan", params.keterangan);
                    $(".btn-batal").attr("no_mmpem", params.no_mmpem);
                }
            },
        });
    },

   setPlasmaSelected: () => {

    $(".tbl_detail tbody tr").each(function(){

        $(this).find('[data-tipe]').each(function(){
            $(this).priceFormat(Config[$(this).data('tipe')]);
        });

        let $row = $(this).closest("tr");
        $row.find("td:eq(1) .plasma").trigger('change');



       setTimeout(() => {

            let noreg_selected = $row.find('.noreg-selected').text()?.trim();
            let $noreg = $row.find('.noreg');

            $noreg.data('skip', true);

            $noreg.val(noreg_selected);

            $noreg.trigger('change'); 

            $noreg.data('skip', false); 

        }, 200);

    })
    


}
};

mm.start_up();