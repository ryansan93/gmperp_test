var pba = {
    searchTimer: null,

    start_up: function () {
        if ($('#form-pelunasan').length) {
            pba.init_datepickers();
            pba.init_pelunasan();
        } else if ($('#detail-tempo').length) {
            pba.init_datepickers();
            pba.init_tempo();
        } else if ($('#form-tdp').length) {
            pba.init_datepickers();
            pba.init_dp();
            pba.init_cicilan();
        } else {
            pba.bind_tab_events();
            pba.bind_search_filter();
            pba.load_data();
            pba.init_selects();
            pba.init_datepickers();
            pba.refresh_asset_summary();
        }

        $.when(
            pba.load_tdp(),       
            pba.load_dp(),       
            pba.load_cicilan()    
        ).done(function (resTdp, resDp, resCicilan) {
            

            console.log("Semua data berhasil dimuat, memulai perhitungan...");
            pba.load_hitung_cicilan(); 
            
    
            pba.load_pelunasan();
            pba.load_tempo();

        }).fail(function (xhr, status, error) {
    
            console.error("Gagal memuat data pembiayaan:", error);
            
        });
        pba.init_datepickers();
    },

    bind_tab_events: function () {
        $('a[data-toggle="tab"]').off('click.pbaTab').on('click.pbaTab', function (event) {
            event.preventDefault();
            var target = $(this).attr('href');
            $('.nav-tabs .nav-item').removeClass('active');
            $('.nav-tabs .nav-link').removeClass('active');
            $('.tab-pane').removeClass('active in');
            $(this).parent('.nav-item').addClass('active');
            $(this).addClass('active');
            $(target).addClass('active in');

            if (typeof $().tab === 'function') {
                $(this).tab('show');
            }
            if (target === '#history') {
                $('#tab-action-content').empty();
            } else if (target === '#action' && $('#tab-action-content').is(':empty')) {
                pba.add_form();
            }
        });
    },

    init_selects: function () {
        if (!$.fn.select2) {
            return;
        }
        $('#kode_aset, #id_supplier, #id_leasing, #filter_kategori_aset').each(function () {
            var select = $(this);
            if (select.data('select2')) {
                select.select2('destroy');
            }
            var isFilter = select.attr('id') === 'filter_kategori_aset';
            select.select2({
                width: '100%',
                allowClear: isFilter,
                placeholder: isFilter ? 'All' : 'Pilih data...',
                dropdownParent: isFilter ? $('#history') : $('#tab-action-content')
            });
        });
        $('#kode_aset').off('change.pba').on('change.pba', pba.refresh_asset_summary);
    },

    // init_datepickers: function () {
    //     if (!$.fn.datetimepicker) {
    //         return;
    //     }

    //     moment.locale('id');
    //     $('#tgl_tdp, #tgl_dp, #tgl_pelunasan').each(function () {
    //         var value = $(this).val();
    //         var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);
    //         if (value && date.isValid()) {
    //             $(this).val(date.format('DD MMMM YYYY'));
    //         }
    //     });

    //     $('#tgl_tdp_picker, #tgl_dp_picker, #tgl_pelunasan_picker').each(function () {
    //         var picker = $(this);
    //         if (!picker.data('DateTimePicker')) {
    //             picker.datetimepicker({
    //                 locale: 'id',
    //                 format: 'DD MMMM YYYY',
    //                 useCurrent: false
    //             });
    //         }
    //     });
    // },

    init_datepickers: function () {
        if (!$.fn.datetimepicker) {
            return;
        }

        moment.locale('id');
        $('#tgl_tdp, #tgl_dp, #tgl_pelunasan, #tgl_jt, #tgl_tdp_clone').each(function () {
            var value = $(this).val();
            var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);
            if (value && date.isValid()) {
                $(this).val(date.format('DD MMMM YYYY'));
            }
        });

        $('#tgl_tdp_picker, #tgl_dp_picker, #tgl_pelunasan_picker, #tgl_jt_picker, #tgl_tdp_clone_picker').each(function () {
            var picker = $(this);
            if (!picker.data('DateTimePicker')) {
                picker.datetimepicker({
                    locale: 'id',
                    format: 'DD MMMM YYYY',
                    useCurrent: false
                });
            }
        });


        $('#tgl_jt_picker').on('dp.change change.datetimepicker', function (e) {

            if (typeof pba !== 'undefined' && typeof pba.hitung_cicilan === 'function') {
                pba.hitung_cicilan();
            }
        });

        $('.tempo-jatuh-tempo-picker').each(function () {
            var picker = $(this);
            if (!picker.data('DateTimePicker')) {
                picker.datetimepicker({
                    locale: 'id',
                    format: 'DD MMMM YYYY',
                    useCurrent: false
                });
            }

            var input = picker.find('.tempo-jatuh-tempo');
            var value = input.val();
            var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);
            if (value && date.isValid()) {
                input.val(date.format('DD MMMM YYYY'));
            }
        });
    },


    init_pelunasan: function () {
        var panel = $('#detail-pelunasan');
        if (!panel.length) {
            return;
        }

        var canMutate = panel.attr('data-can-mutate') === '1';

        $(document)
            // .off('click.pelunasanAdd', '#pelunasan-add-row')
            // .on('click.pelunasanAdd', '#pelunasan-add-row', function () {
            //     if (!canMutate) {
            //         return;
            //     }
            //     var row = $('<div class="row pelunasan-row" style="margin-bottom:8px;">' +
            //         '<div class="col-xs-7"><input type="text" class="form-control pelunasan-deskripsi" maxlength="255" placeholder="Deskripsi"></div>' +
            //         '<div class="col-xs-4"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right pelunasan-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
            //         '<div class="col-xs-1"><button type="button" class="btn btn-danger btn-sm pelunasan-remove-row" title="Hapus baris"><i class="fa fa-times"></i></button></div>' +
            //         '</div>');
            //     $('#pelunasan-rows').append(row);
            //     pba.update_pelunasan_total();
            // })
            // .off('submit.pelunasanSave', '#form-pelunasan')
            // .on('submit.pelunasanSave', '#form-pelunasan', function (event) {
            //     event.preventDefault();
            //     pba.save_pelunasan();
            // })
            // .off('click.pelunasanDelete', '#pelunasan-delete')
            // .on('click.pelunasanDelete', '#pelunasan-delete', function () {
            //     pba.delete_pelunasan(this);
            // })
            .off('click.pelunasanRemove', '.pelunasan-remove-row')
            .on('click.pelunasanRemove', '.pelunasan-remove-row', function () {
                if (!canMutate) {
                    return;
                }
                // $(this).closest('.pelunasan-row').remove();
                // pba.update_pelunasan_rows();
                // pba.update_pelunasan_total();
            })
            .off('input.pelunasanNominal', '.pelunasan-nominal')
            .on('input.pelunasanNominal', '.pelunasan-nominal', function () {
                if (canMutate && !$(this).prop('readonly')) {
                    $(this).val(pba.format_rupiah($(this).val()));
                    pba.update_pelunasan_total();
                }
            });

        $('.pelunasan-nominal').each(function () {
            $(this).val(pba.format_rupiah($(this).val()));
        });
        pba.update_pelunasan_rows();
        pba.update_pelunasan_total();
    },


    addRowPelunasan: function(){
        var row = $('<div class="row pelunasan-row" style="margin-bottom:8px;">' +
            '<div class="col-xs-7"><input type="text" class="form-control pelunasan-deskripsi" maxlength="255" placeholder="Deskripsi"></div>' +
            '<div class="col-xs-4"><div class="input-group"><span class="input-group-addon">Rp</span><input type="int"   oninput="pba.inputRupiah(this); pba.update_pelunasan_total();" class="form-control text-right pelunasan-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
            '<div class="col-xs-1"><button type="button" onclick="pba.removeRowPelunasan(this)" class="btn btn-danger btn-sm pelunasan-remove-row" title="Hapus baris"><i class="fa fa-times"></i></button></div>' +
            '</div>');
        $('#pelunasan-rows').append(row);
        pba.update_pelunasan_total();
    },


    removeRowPelunasan : function(elm){
          $(elm).closest('.pelunasan-row').remove();
                // pba.update_pelunasan_rows();
                // pba.update_pelunasan_total();
    },


    init_tempo: function () {
        var panel = $('#detail-tempo');
        if (!panel.length) {
            return;
        }

        var canMutate = panel.attr('data-can-mutate') === '1';

        $(document)
            .off('click.tempoGenerate', '#btn_tambah_termin')
            .on('click.tempoGenerate', '#btn_tambah_termin', function () {
                if (!canMutate) {
                    return;
                }
                pba.generate_tempo_rows();
            })
    
            // .off('change.tempoTermin', '#jumlah_termin')
            // .on('change.tempoTermin', '#jumlah_termin', function () {
            //     if (canMutate) {
            //         pba.generate_tempo_rows();
            //     }
            // })
            
            .off('click.tempoRemove', '.tempo-remove-row')
            .on('click.tempoRemove', '.tempo-remove-row', function () {
                if (!canMutate) {
                    return;
                }
                $(this).closest('.tempo-row').remove();
                pba.renumber_tempo_rows();
                pba.hitung_tempo();
            })
            // Submit form
            .off('submit.tempoSave', '#form-tempo')
            .on('submit.tempoSave', '#form-tempo', function (event) {
                event.preventDefault();
                pba.save_rencana_tempo();
            })
            // Simpan button
            .off('click.tempoSaveBtn', '#btn_simpan_tempo')
            .on('click.tempoSaveBtn', '#btn_simpan_tempo', function () {
                pba.save_rencana_tempo();
            });

        // Input biaya lain
        $(document)
            .off('input.tempoHeader', '#biaya_lain_tempo')
            .on('input.tempoHeader', '#biaya_lain_tempo', function () {
                if (canMutate) {
                    $(this).val(pba.format_rupiah($(this).val()));
                    pba.hitung_tempo();
                }
            });

        // Format rupiah untuk nominal yang sudah ada
        $('.tempo-nominal').each(function () {
            $(this).val(pba.format_rupiah($(this).val()));
        });

        // Bind events untuk row yang sudah ada dari server
        $('#tempo-rows .tempo-row').each(function () {
            pba.bind_tempo_row_events($(this));
        });

        pba.hitung_tempo();
    },


    load_tempo : function() {
       
        let kode = $("#key_kode_pembiayaan").val() ?? kode_pembiayaan

        $.ajax({
            url: 'aset/PembiayaanAset/load_tempo',
            type: 'POST',
            dataType: 'html',
            data: { kode: kode },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (resp) {
                hideLoading();
                $('.load_tempo').html(resp)
                pba.init_datepickers()
                
            },
            error: function () {
                hideLoading();
                button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan tempo gagal diproses.');
            }
        });

    },

    proses_pembiayaan: function(elm) {
        var $tab = $('a[href="#action"]');
        $tab.tab('show');
        $('a[href="#history"]').closest('li').removeClass('active');
        $('#action').addClass('active');
        $('#action').closest(".nav-item").addClass('active');


        var button      = $(elm);
        var id          = encodeURIComponent(button.data('id'));
        var kodeJenis   = String(button.data('kode-jenis-pembiayaan') || '').toUpperCase();
        
        var urlMap = {
            'PMB01': 'aset/PembiayaanAset/ProsesCash',
            'PMB02': 'aset/PembiayaanAset/ProsesTempo',
            'PMB03': 'aset/PembiayaanAset/ProsesLeasing'
        };
        
        var url = urlMap[kodeJenis] || ('aset/PembiayaanAset/ProsesLeasing');


        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'html',
            data: { id: id },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                $("#tab-action-content").html(response)

                pba.start_up();
                
            },
            error: function () {
                hideLoading();
                button.prop('disabled', false);
                bootbox.alert('Permintaan pelunasan gagal diproses.');
            }
        });


        
    },


    generate_tempo_rows: function () {
        var jumlahTermin = Number($('#jumlah_termin').val() || 0);
        if (jumlahTermin < 1) {
            $('#tempo-rows').empty();
            pba.hitung_tempo();
            return;
        }

        var currentRows = $('#tempo-rows .tempo-row').length;

        // Jika jumlah baris sudah pas, tidak perlu generate ulang (hindari reset data)
        if (currentRows === jumlahTermin) {
            return;
        }

        // Jika baris lebih banyak dari jumlah termin, hapus kelebihannya
        if (currentRows > jumlahTermin) {
            $('#tempo-rows .tempo-row').each(function (index) {
                if (index >= jumlahTermin) {
                    $(this).remove();
                }
            });
            pba.renumber_tempo_rows();
            pba.hitung_tempo();
            return;
        }

        // Jika baris kurang, tambahkan sampai pas
        for (var i = currentRows + 1; i <= jumlahTermin; i++) {
            var row = $('<div class="row tempo-row" style="margin-bottom:8px;">' +
                '<div class="col-xs-1" style="padding-top:7px;"><strong>' + i + '</strong></div>' +
                '<div class="col-xs-5"><div class="input-group date tempo-jatuh-tempo-picker"><input type="text" class="form-control tempo-jatuh-tempo" placeholder="Pilih Tanggal"><span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>' +
                '<div class="col-xs-5"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right tempo-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
                '<div class="col-xs-1 text-right"><button type="button" class="btn btn-danger btn-sm tempo-remove-row"><i class="fa fa-times"></i></button></div>' +
                '</div>');
            $('#tempo-rows').append(row);
            if ($.fn.datetimepicker) {
                row.find('.tempo-jatuh-tempo-picker').datetimepicker({
                    locale: 'id',
                    format: 'DD MMMM YYYY',
                    useCurrent: false
                });
            }
            pba.bind_tempo_row_events(row);
        }

        pba.hitung_tempo();
    },

    bind_tempo_row_events: function (row) {
        row.find('.tempo-nominal').off('input.tempoNominal').on('input.tempoNominal', function () {
            $(this).val(pba.format_rupiah($(this).val()));
            pba.hitung_tempo();
        });
        row.find('.tempo-jatuh-tempo').off('change.tempoDate').on('change.tempoDate', function () {
            pba.hitung_tempo();
        });
    },

    renumber_tempo_rows: function () {
        $('#tempo-rows .tempo-row').each(function (index) {
            $(this).find('div:first strong').text(index + 1);
        });
    },

    hitung_tempo: function () {
        var panel = $('#detail-tempo');
        if (!panel.length) {
            return;
        }

        var hargaBeli = Number(panel.data('harga-beli') || 0);
        var tdp = Number(panel.data('tdp-total') || 0);
        var biayaLain = Number(pba.to_backend_nominal($('#biaya_lain_tempo').val()));
        var jumlahTermin = Number($('#jumlah_termin').val() || 0);

        var totalPembelian = hargaBeli + biayaLain;
        var sisa = totalPembelian - tdp;

        var totalTermin = 0;
        var validRows = 0;
        $('#tempo-rows .tempo-row').each(function () {
            var nominal = Number(pba.to_backend_nominal($(this).find('.tempo-nominal').val()));
            var tanggal = $(this).find('.tempo-jatuh-tempo').val();
            totalTermin += nominal;
            if (nominal > 0 && tanggal) {
                validRows++;
            }
        });

        $('#display_biaya_lain').text(pba.format_rupiah(biayaLain));
        $('#display_total_pembelian').text(pba.format_rupiah(totalPembelian));
        $('#display_tdp').text('(' + pba.format_rupiah(tdp) + ')');
        $('#display_sisa').text(pba.format_rupiah(sisa));
        $('#display_total_termin').text(pba.format_rupiah(totalTermin));

        var selisih = sisa - totalTermin;
        var isMatch = (validRows === jumlahTermin) && (selisih === 0) && (jumlahTermin > 0);

        if (jumlahTermin > 0 && selisih !== 0) {
            $('#selisih_tempo').text('Selisih: ' + pba.format_rupiah(selisih)).removeClass('text-success').addClass('text-danger');
        } else if (jumlahTermin > 0 && selisih === 0) {
            $('#selisih_tempo').text('✓ Pas').removeClass('text-danger').addClass('text-success');
        } else {
            $('#selisih_tempo').text('');
        }

        $('#btn_simpan_tempo').prop('disabled', !isMatch);
    },

    save_rencana_tempo: function () {
        var panel = $('#detail-tempo');
        if (panel.attr('data-can-mutate') !== '1') {
            return;
        }

        var jumlahTermin = Number($('#jumlah_termin').val() || 0);
        if (jumlahTermin < 1) {
            bootbox.alert('Jumlah termin harus diisi.');
            return;
        }

        var rows = [];
        var valid = true;
        $('#tempo-rows .tempo-row').each(function (index) {
            var tanggalStr = $(this).find('.tempo-jatuh-tempo').val();
            var tanggal = moment(tanggalStr, 'DD MMMM YYYY', 'id', true);
            var nominal = pba.to_backend_nominal($(this).find('.tempo-nominal').val());

            if (!tanggal.isValid()) {
                bootbox.alert('Jatuh tempo termin ke-' + (index + 1) + ' tidak valid.');
                valid = false;
                return false; // break each
            }
            if (Number(nominal) <= 0) {
                bootbox.alert('Nominal termin ke-' + (index + 1) + ' harus lebih dari 0.');
                valid = false;
                return false;
            }

            rows.push({
                jatuh_tempo: tanggal.format('YYYY-MM-DD'),
                nominal: nominal
            });
        });

        if (!valid) {
            return;
        }

        if (rows.length !== jumlahTermin) {
            bootbox.alert('Jumlah baris termin harus sama dengan jumlah termin (' + jumlahTermin + ').');
            return;
        }

        var params = {
            kode_pembiayaan: $('#kode_pembiayaan_tempo').val(),
            harga_beli: String(panel.data('harga-beli')),
            biaya_lain: pba.to_backend_nominal($('#biaya_lain_tempo').val()),
            jumlah_termin: String(jumlahTermin),
            rows: rows
        };

        pba.tdp_request('aset/PembiayaanAset/save_rencana_tempo', params, $('#btn_simpan_tempo'));
    },

    update_pelunasan_rows: function () {
        var rows = $('#pelunasan-rows .pelunasan-row');
        var canMutate = $('#detail-pelunasan').attr('data-can-mutate') === '1';
        rows.each(function (index) {
            $(this).find('.pelunasan-remove-row').prop('disabled', !canMutate || index < 2);
        });
    },

    update_pelunasan_total: function () {
        var totalCents = 0;
        $('#pelunasan-rows .pelunasan-nominal').each(function () {
            totalCents += Number(pba.to_backend_nominal($(this).val())) * 100;
        });
        var tandaJadiCents = Math.round(Number($('#detail-pelunasan').attr('data-tanda-jadi-total') || 0) * 100);
        var total = totalCents / 100;
        var diajukan = (totalCents - tandaJadiCents) / 100;
        $('#total-pembelian').text(total.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        $('#dikurangi-tanda-jadi').text('(' + (tandaJadiCents / 100).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ')');
        $('#diajukan').text(diajukan.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    },

    save_pelunasan: function (elm, e) {
        // var panel = $('#detail-pelunasan');
        // if (panel.attr('data-can-mutate') !== '1') {
        //     return;
        // }

        var date = moment($('#tgl_pelunasan').val(), 'DD MMMM YYYY', 'id', true);
        if (!date.isValid()) {
            bootbox.alert('Tanggal pelunasan wajib dipilih.');
            return;
        }

        var rows = [];
        var biayaLain = 0;
        var valid = true;

        $('#pelunasan-rows .pelunasan-row').each(function (index) {
            var $row = $(this);
            var deskripsi = $.trim($row.find('.pelunasan-deskripsi').val() || '');
            var $nominalInput = $row.find('.pelunasan-nominal');
            var isReadonly = $nominalInput.attr('readonly') !== undefined;
            var rawNominal = $nominalInput.val() || '0';
            var amount = pba.to_backend_nominal(rawNominal);
            // var diajukan = pba.to_backend_nominal($('#diajukan').html())

            if (!isReadonly) {
                biayaLain += Number(amount);
            }

            rows.push({
                urutan: index + 1,
                deskripsi: deskripsi,
                nominal: String(amount),
                is_harga_beli: isReadonly ? 1 : 0
            });
        });

        if (!Number.isSafeInteger(biayaLain) || biayaLain < 0) {
            valid = false;
        }
        if (!valid) {
            bootbox.alert('Nominal biaya lain tidak valid.');
            return;
        }

        var params = {
            harga_beli: $(".harga_beli").val() || '',
            kode_pembiayaan: $('#key_kode_pembiayaan').val(),
            tanggal_pelunasan: date.format('YYYY-MM-DD'),
            // total_pembelian : $('#total-pembelian').html(),
            // tdp : $('#dikurangi-tanda-jadi').html(),
            diajukan : pba.to_backend_nominal($('#diajukan').html()),
            rows: rows  
        };


        // console.log(params);
        $.ajax({
            url: 'aset/PembiayaanAset/save_pelunasan',
            type: 'POST',
            dataType: 'JSON',
            data: { params: params },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                if (response.status == 1) {
                    bootbox.alert(response.message, function () {
                        pba.load_tdp(params.kode_pembiayaan);
                        pba.load_pelunasan(params.kode_pembiayaan);
                    });
                } else {
                    // button.prop('disabled', false);
                    bootbox.alert(response.message || 'Data gagal disimpan.');
                }
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan DP gagal diproses.');
            }
        });
    },

    delete_pelunasan: function (element) {
        var button = $(element);
        bootbox.confirm('Hapus data pelunasan ini?', function (confirmed) {
            if (confirmed) {
                pba.pelunasan_request('aset/PembiayaanAset/delete_pelunasan', {
                    id: button.data('id')
                }, button);
            }
        });
    },

    pelunasan_request: function (url, params, button) {
        button.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: { params: params },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                if (response.status == 1) {
                    bootbox.alert(response.message, function () {
                        window.location.reload(true);
                    });
                } else {
                    button.prop('disabled', false);
                    bootbox.alert(response.message || 'Pelunasan gagal disimpan.');
                }
            },
            error: function () {
                hideLoading();
                button.prop('disabled', false);
                bootbox.alert('Permintaan pelunasan gagal diproses.');
            }
        });
    },

    // init_dp: function () {
    //     $(document)
    //         .off('click.dpAdd', '#dp-add-row')
    //         .on('click.dpAdd', '#dp-add-row', function () {
    //             var row = $('<div class="row dp-row" style="margin-bottom:8px;">' +
    //                 '<input type="hidden" class="dp-id" value="">' +
    //                 '<div class="col-xs-8"><input type="text" class="form-control dp-komponen" placeholder="Jenis komponen (contoh: UANG_MUKA)"></div>' +
    //                 '<div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
    //                 '<div class="col-xs-1 text-right"><button type="button" class="btn btn-danger btn-sm dp-remove-row" title="Hapus baris"><i class="fa fa-times"></i></button></div>');
    //             $('#dp-rows').append(row);
    //         })
    //         .off('click.dpAddInstallment', '#dp-add-installment')
    //         .on('click.dpAddInstallment', '#dp-add-installment', function () {
    //             var installmentNumber = $('#dp-installment-rows .dp-installment-row').length + 1;
    //             var row = $('<div class="row dp-installment-row" style="margin-bottom:8px;">' +
    //                 '<input type="hidden" class="dp-id" value="">' +
    //                 '<div class="col-xs-2"><label style="padding-top:7px;">Angsuran ke</label></div>' +
    //                 '<div class="col-xs-2"><input type="number" class="form-control dp-angsuran-ke" min="1" value="' + installmentNumber + '" placeholder="Ke"></div>' +
    //                 '<div class="col-xs-3"><div class="input-group date dp-jatuh-tempo-picker"><input type="text" class="form-control dp-jatuh-tempo" autocomplete="off" placeholder="Jatuh tempo"><span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>' +
    //                 '<div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
    //                 '<div class="col-xs-2 text-right"><button type="button" class="btn btn-danger btn-sm dp-remove-row" title="Hapus angsuran"><i class="fa fa-times"></i></button></div></div>');
    //             $('#dp-installment-rows').append(row);
    //             if ($.fn.datetimepicker) {
    //                 row.find('.dp-jatuh-tempo-picker').datetimepicker({
    //                     locale: 'id',
    //                     format: 'DD MMMM YYYY',
    //                     useCurrent: false
    //                 });
    //             }
    //         })
    //         .off('click.dpRemove', '.dp-remove-row')
    //         .on('click.dpRemove', '.dp-remove-row', function () {
    //             $(this).closest('.dp-row, .dp-installment-row').remove();
    //             pba.update_dp_total();
    //         })
    //         .off('input.dpNominal', '.dp-nominal')
    //         .on('input.dpNominal', '.dp-nominal', function () {
    //             $(this).val(pba.format_rupiah($(this).val()));
    //             pba.update_dp_total();
    //         })
    //         .off('click.dpDueDate', '.dp-jatuh-tempo-picker .input-group-addon')
    //         .on('click.dpDueDate', '.dp-jatuh-tempo-picker .input-group-addon', function () {
    //             var picker = $(this).closest('.dp-jatuh-tempo-picker');
    //             if (picker.data('DateTimePicker')) {
    //                 picker.datetimepicker('show');
    //             }
    //         })
    //         .off('input.dpComponent', '.dp-komponen')
    //         .on('input.dpComponent', '.dp-komponen', function () {
    //             pba.update_dp_total();
    //         })
    //         .off('click.dpDelete', '.dp-delete-row')
    //         .on('click.dpDelete', '.dp-delete-row', function () {
    //             pba.delete_dp(this);
    //         })
    //         .off('submit.dpSave', '#form-dp')
    //         .on('submit.dpSave', '#form-dp', function (event) {
    //             event.preventDefault();
    //             pba.save_dp();
    //         });

    //     $('.dp-nominal').each(function () {
    //         $(this).val(pba.format_rupiah($(this).val()));
    //     });
    //     $('.dp-jatuh-tempo').each(function () {
    //         var value = $(this).val();
    //         var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);
    //         if (value && date.isValid()) {
    //             $(this).val(date.format('DD MMMM YYYY'));
    //         }
    //     });
    //     if ($.fn.datetimepicker) {
    //         $('.dp-jatuh-tempo-picker').each(function () {
    //             if (!$(this).data('DateTimePicker')) {
    //                 $(this).datetimepicker({
    //                     locale: 'id',
    //                     format: 'DD MMMM YYYY',
    //                     useCurrent: false
    //                 });
    //             }
    //         });
    //     }
    //     $('.dp-komponen').trigger('change');
    //     pba.update_dp_total();
    // },


    dpAddRow: function() {
         var row = $('<div class="row dp-row" style="margin-bottom:8px;">' +
                    '<input type="hidden" class="dp-id" value="">' +
                    '<div class="col-xs-8"><input type="text" class="form-control dp-komponen" placeholder="Jenis komponen (contoh: UANG_MUKA)"></div>' +
                    '<div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" oninput="pba.inputRupiah(this);" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
                    '<div class="col-xs-1 text-right"><button type="button" class="btn btn-danger btn-sm dp-remove-row" onclick="pba.dpRemoveRow(this)" title="Hapus baris"><i class="fa fa-times"></i></button></div>');
        $('#dp-rows').append(row);

        pba.update_dp_total();
    },

    dpRemoveRow: function(element) {
        $(element).closest('.dp-row, .dp-installment-row').remove();
        pba.update_dp_total();
    },

    dpAddAngsuranRow: function() {
        var installmentNumber = $('#dp-installment-rows .dp-installment-row').length + 1;
        var row = $('<div class="row dp-installment-row" style="margin-bottom:8px;">' +
            '<input type="hidden" class="dp-id" value="">' +
            '<div class="col-xs-5"><label style="padding-top:7px;">Angsuran ke</label></div>' +
            '<div class="col-xs-3"><input type="number" class="form-control dp-angsuran-ke" min="1" value="' + installmentNumber + '" placeholder="Ke"></div>' +
            '<div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" oninput="pba.inputRupiah(this);" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
            '<div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveAngsuranRow(this)" class="btn btn-danger btn-sm dp-remove-row" title="Hapus angsuran"><i class="fa fa-times"></i></button></div></div>');
        $('#dp-installment-rows').append(row);
    },

    dpRemoveAngsuranRow : function (elment)
    {
        $(elment).closest('.dp-row, .dp-installment-row').remove();
        pba.update_dp_total();
    },

    update_dp_total: function () {
        var total = 0;
        var uangMuka = 0;
        var biayaPenambahHutang = 0; 

        $('#dp-rows .dp-row').each(function () {
            var nominal = Number(pba.to_backend_nominal($(this).find('.dp-nominal').val()));
            total += nominal;
            
            var componentInput = $(this).find('.dp-komponen');
            var component = componentInput.length
                ? $.trim(componentInput.val() || '').toUpperCase()
                : 'UANG_MUKA';
                
            if (component === 'UANG_MUKA') {
                uangMuka += nominal;
            } else {
                biayaPenambahHutang += nominal; 
            }
        });

        $('#dp-installment-rows .dp-nominal').each(function () {
            total += Number(pba.to_backend_nominal($(this).val()));
        });

        $('#dp-total').text(total.toLocaleString('id-ID', { maximumFractionDigits: 0 }));
        
        $('#detail-cicilan').attr('data-dp-uang-muka', uangMuka);
        $('#detail-cicilan').attr('data-biaya-penambah', biayaPenambahHutang);
    },

    init_cicilan: function () {
        $('#tenor, #bunga_flat, #tgl_jt')
            .off('input.cicilan change.cicilan')
            .on('input.cicilan change.cicilan', function () {
                pba.hitung_cicilan();
            });
        $(document)
            .off('click.cicilanSave', '#simpan-detail-cicilan')
            .on('click.cicilanSave', '#simpan-detail-cicilan', function () {
                pba.simpan_detail_cicilan(this);
            });

   
        // pba.hitung_cicilan();
    },

    // hitung_cicilan: function () {
    //     var panel = $('#detail-cicilan');
    //     if (!panel.length) return;

    //     var cents = function (value) {
    //         var amount = Number(value);
    //         return Number.isFinite(amount) ? Math.round(amount * 100) : 0;
    //     };
        
    //     var formatMoney = function (valueInCents) {
    //         return Math.round(valueInCents / 100).toLocaleString('id-ID');
    //     };

    //     var priceCents = cents($("#cicilan-harga-beli").html());

    //     console.log(priceCents);
    //     var tdpCents = cents(panel.attr('data-tdp-pengurang'));
    //     var downPaymentCents = cents(panel.attr('data-dp-uang-muka'));
    //     var principalCents = priceCents - downPaymentCents - tdpCents;
    //     var tbody = $('#jadwal-cicilan');

    //     $('#cicilan-harga-beli').text(formatMoney(priceCents));
    //     $('#cicilan-tdp-pengurang').text('(' + formatMoney(tdpCents) + ')');
    //     $('#cicilan-dp-uang-muka').text('(' + formatMoney(downPaymentCents) + ')');
    //     $('#cicilan-pokok-hutang').text(formatMoney(Math.max(principalCents, 0)));

    //     var showMessage = function (msg) {
    //         tbody.empty().append($('<tr>').append($('<td>').attr('colspan', 8).addClass('text-center text-muted').text(msg)));
    //     };

    //     if (principalCents < 0) {
    //         showMessage('Total TDP dan uang muka melebihi harga beli aset.');
    //         return;
    //     }

    //     var tenor = Number($('#tenor').val());
    //     var annualRate = Number($('#bunga_flat').val());
        
    //     if (!tenor || tenor < 1) {
    //         showMessage('Jadwal cicilan akan tampil setelah tenor diisi.');
    //         return;
    //     }
        
    //     if (!annualRate || isNaN(annualRate) || annualRate <= 0) {
    //         showMessage('Jadwal cicilan akan tampil setelah bunga flat diisi.');
    //         return;
    //     }

    //     var monthlyRate          = (annualRate / 100) / 12;
    //     var totalMonths          = tenor;
        
    //     var baseInstallmentCents = Math.round(
    //         principalCents * (monthlyRate / (1 - Math.pow(1 + monthlyRate, -totalMonths)))
    //     );

    //     var totalPaymentCents   = baseInstallmentCents * tenor;
    //     var totalInterestCents  = totalPaymentCents - principalCents;

    //     $('#cicilan-nominal-bulanan').text(formatMoney(baseInstallmentCents));
    //     $('.dp-installment-row').find('.dp-nominal').val(formatMoney(baseInstallmentCents));

    //     $('#cicilan-tenor-label').text(tenor);
    //     $('#cicilan-total-bunga').text(formatMoney(totalInterestCents));
    //     $('#cicilan-total-ar').text(formatMoney(totalPaymentCents));
    //     $('#cicilan-selisih-pembulatan').text('');
    //     $('#simpan-detail-cicilan').prop('disabled', $('#simpan-detail-cicilan').attr('data-can-save') !== '1');

    //     tbody.empty();
        
    //     var currentBalance = principalCents; 
        
    //     var startDate = moment(panel.attr('data-tanggal-dasar') || panel.attr('data-tanggal-pembiayaan'), 'YYYY-MM-DD', true);
    //     if (!startDate.isValid()) startDate = moment();

    //     var hasDpAngsuran1 = panel.attr('data-has-dp-angsuran-1') === '1' || panel.attr('data-dp-angsuran-1') === '1';
    //     // console.log(hasDpAngsuran1)
    //     // return false

    //     for (var i = 1; i <= tenor; i++) {
    //         var dueDate = startDate.clone().add(i, 'months');
            
    //         var bunga = currentBalance * monthlyRate;
    //         var pokok = baseInstallmentCents - bunga;
    //         var nominal = baseInstallmentCents;
            
    //         if (i === tenor) {
    //             pokok = currentBalance;
    //             bunga = baseInstallmentCents - pokok;
                
    //             if (bunga < 0) {
    //                 bunga = 0;
    //                 nominal = pokok; 
    //             }
    //         }
            
    //         currentBalance -= pokok;
    //         if (currentBalance < 0) currentBalance = 0;
            
    //         var displayPokok = Math.round(pokok);
    //         var displayBunga = Math.round(bunga);
    //         var displayBalance = Math.round(currentBalance);
    //         var displayNominal = Math.round(nominal);

    //         var isDpAngsuran1 = (i === 1 && hasDpAngsuran1);
    //         var jenisLabel  = isDpAngsuran1 ? '<span class="label label-success">DP</span>' : '<span class="label label-default">CICILAN</span>';
    //         var statusLabel = isDpAngsuran1 ? '<span class="label label-success">Lunas</span>' : '<span class="label label-warning">Pending</span>';

    //         var row = $('<tr>');
    //         row.append($('<td class="text-center">').text(i));                                          
    //         row.append($('<td class="text-left">').text(dueDate.format('DD MMMM YYYY')));               
    //         row.append($('<td class="text-center">').html(jenisLabel)); 
    //         row.append($('<td class="text-right">').text(formatMoney(displayPokok)));                      
    //         row.append($('<td class="text-right">').text(formatMoney(displayBunga)));                      
    //         row.append($('<td class="text-right">').text(formatMoney(displayNominal)));            
    //         row.append($('<td class="text-right">').text(formatMoney(displayBalance)));             
    //         row.append($('<td class="text-center">').html(statusLabel)); 
    //         tbody.append(row);
    //     }
    // },

    hitung_cicilan: function () {
        
        var panel = $('#detail-cicilan');
        if (!panel.length) return;

        var tbody = $('#jadwal-cicilan');
        
        function getCleanInteger(value) {
            value = String(value || '').trim();
            let clean = value.replace(/[^\d-]/g, '');
            return parseInt(clean, 10) || 0;
        }

        var formatMoney = function (value) {
            return Math.round(value).toLocaleString('id-ID');
        };

        var dateValue = $('#tgl_jt').val();
        var date = moment(dateValue, 'DD MMMM YYYY', 'id', true);
        if (!date.isValid()) {
            return;
        }

        let params = {
            harga_beli: getCleanInteger($("#cicilan-harga-beli").text()),
            tdp_pengurang: getCleanInteger($("#cicilan-tdp-pengurang").text()),
            dp_uang_muka: getCleanInteger($("#cicilan-dp-uang-muka").text()),
            sisa: getCleanInteger($("#cicilan-sisa").text()),
            pokok_hutang: getCleanInteger($("#cicilan-pokok-hutang").text()),
            biaya_prepaid: getCleanInteger($("#cicilan-biaya-prepaid").text()),
            total_perolehan: getCleanInteger($("#cicilan-total-perolehan").text()),
            total_bunga: getCleanInteger($("#cicilan-total-bunga").text()),
            tanggal_jatuh_tempo: date,
        };

        // 1. Hitung Pokok Hutang
        var principal = params.harga_beli - params.dp_uang_muka - params.tdp_pengurang;

        if (principal < 0) {
            bootbox.alert('Total TDP dan uang muka melebihi harga beli aset.');
            return;
        }

        var tenor       = Number($('#tenor').val());
        var annualRate  = Number($('#bunga_flat').val());
        
        if (!tenor || tenor < 1) return;
        if (!annualRate || isNaN(annualRate) || annualRate <= 0) return;

        // 2. RUMUS ANUITAS (sesuai gambar Excel Anda)
        // Cicilan = P × r ÷ (1 − (1 + r)^−n)
        var monthlyRate = (annualRate / 100) / 12;
        var baseInstallment = Math.round(
            principal * monthlyRate / (1 - Math.pow(1 + monthlyRate, -tenor))
        );

        var totalPayment = baseInstallment * tenor;
        var totalInterest = totalPayment - principal;

        // 3. UPDATE NILAI DI UI
        $('#cicilan-nominal-bulanan').text(formatMoney(baseInstallment));
        $('#cicilan-tenor-label').text(tenor);
        $('#cicilan-total-bunga').text(formatMoney(totalInterest));
        $('#cicilan-total-ar').text(formatMoney(totalPayment));

        // 4. GENERATE TABEL JADWAL CICILAN (Anuitas: Pokok & Bunga berubah tiap bulan)
        tbody.empty();
        
        var currentBalance = principal; 
        var baseDueDate = date.clone();
        var hasDpAngsuran1 = panel.attr('data-has-dp-angsuran-1') === '1' || panel.attr('data-dp-angsuran-1') === '1';

        for (var i = 1; i <= tenor; i++) {
            var dueDate = baseDueDate.clone().add(i - 1, 'months');
            
            // Bunga bulan ini = Sisa Pokok × Rate Bulanan
            var bunga = currentBalance * monthlyRate;
            
            // Pokok bulan ini = Cicilan Tetap - Bunga
            var pokok = baseInstallment - bunga;
            var nominal = baseInstallment;
            
            // Penyesuaian bulan terakhir agar sisa hutang tepat 0
            if (i === tenor) {
                pokok = currentBalance;
                bunga = nominal - pokok;
                
                if (bunga < 0) {
                    bunga = 0;
                    nominal = pokok; 
                }
            }
            
            currentBalance -= pokok;
            if (currentBalance < 0) currentBalance = 0;
            
            var displayPokok = Math.round(pokok);
            var displayBunga = Math.round(bunga);
            var displayBalance = Math.round(currentBalance);
            var displayNominal = Math.round(nominal);

            var isDpAngsuran1 = (i === 1 && hasDpAngsuran1);
            var jenisLabel  = isDpAngsuran1 ? '<span class="label label-success">DP</span>' : '<span class="label label-default">CICILAN</span>';
            var statusLabel = isDpAngsuran1 ? '<span class="label label-success">Lunas</span>' : '<span class="label label-warning">Pending</span>';

            var row = $('<tr>');
            row.append($('<td class="text-center">').text(i));                                          
            row.append($('<td class="text-left">').text(dueDate.format('DD MMMM YYYY')));               
            row.append($('<td class="text-center">').html(jenisLabel)); 
            row.append($('<td class="text-right">').text(formatMoney(displayPokok)));                      
            row.append($('<td class="text-right">').text(formatMoney(displayBunga)));                      
            row.append($('<td class="text-right">').text(formatMoney(displayNominal)));            
            row.append($('<td class="text-right">').text(formatMoney(displayBalance)));             
            row.append($('<td class="text-center">').html(statusLabel)); 
            tbody.append(row);
        }
    },
        
    simpan_detail_cicilan: function (buttonElement) {
        var button = $(buttonElement);
        var panel = $('#detail-cicilan');
        var tenor = $('#tenor').val();
        var bunga = $('#bunga_flat').val();


        var dateValue = $('#tgl_jt').val();
        if (!dateValue) {
            bootbox.alert('Jadwal cicilan akan tampil setelah tanggal jatuh tempo diisi.');
            return; 
        }

        var date = moment(dateValue, 'DD MMMM YYYY', 'id', true);
        
        var tgl_jt = date && date.isValid() ? date.format('YYYY-MM-DD') : '';
        
        if (button.attr('data-can-save') !== '1' || button.prop('disabled')) {
            return;
        }
        
        // 2. Tambahkan validasi tgl_jt agar tidak kosong saat disimpan
        if (!tenor || !bunga || !tgl_jt || !$('#jadwal-cicilan tr').length || $('#jadwal-cicilan td[colspan]').length) {
            bootbox.alert('Lengkapi tenor, bunga, dan tanggal jatuh tempo, lalu pastikan jadwal cicilan sudah terbentuk.');
            return;
        }

        var getCleanInteger = function(selector) {
            var rawText = $(selector).text();
            var withoutThousands = String(rawText).replace(/\./g, '');
            var withDotDecimal = withoutThousands.replace(',', '.');
            var value = parseFloat(withDotDecimal) || 0;
            return String(Math.round(value));
        };

        let dp = parseInt($('#cicilan-dp-uang-muka').attr('dp')) || 0;

        var params = {
            kode_pembiayaan: $('#kode_pembiayaan_dp').val() || $('#kode_pembiayaan_tdp').val(),
            tenor: tenor,
            bunga: bunga,
            tanggal_jatuh_tempo: tgl_jt, // 3. Masukkan ke dalam params
            harga_beli: getCleanInteger('#cicilan-harga-beli'),
            tdp_pengurang: getCleanInteger('#cicilan-tdp-pengurang'),
            dp: dp,
            pokok_hutang: getCleanInteger('#cicilan-pokok-hutang'),
            biaya_prepaid: getCleanInteger('#cicilan-biaya-prepaid'),
            total_perolehan: getCleanInteger('#cicilan-total-perolehan'),
            total_ar: getCleanInteger('#cicilan-total-ar'),
            total_bunga: getCleanInteger('#cicilan-total-bunga')
        };

        // console.log(params);
        
        pba.tdp_request('aset/PembiayaanAset/simpan_detail_cicilan', params, button);
    },

    save_dp: function () {
        var dateValue = $('#tgl_dp').val();
        var date = dateValue ? moment(dateValue, 'DD MMMM YYYY', 'id', true) : null;
        if (dateValue && !date.isValid()) {
            bootbox.alert('Tanggal DP tidak valid.');
            return;
        }

        var rows = [];
        var valid = true;
        $('#dp-rows .dp-row').each(function () {
            var row = $(this);
            if (row.find('.dp-komponen').prop('disabled')) {
                return;
            }
            var componentInput  = row.find('.dp-komponen');
            var component       = componentInput.length ? $.trim(componentInput.val() || '').toUpperCase(): 'UANG_MUKA';
            var nominal         = pba.to_backend_nominal(row.find('.dp-nominal').val());
            
            var data = {
                // id: row.find('.dp-id').val() || '',
                jenis_komponen: component,
                // deskripsi: '',
                nominal: nominal,
                angsuran_ke: '',
                // jatuh_tempo: ''
            };
            if (Number(nominal) <= 0) {
                valid = false;
            }
            rows.push(data);
        });
       
        $('#dp-installment-rows .dp-installment-row').each(function () {
            var row = $(this);
            if (row.find('.dp-angsuran-ke').prop('disabled')) {
                return;
            }
            // var dueDate = moment(row.find('.dp-jatuh-tempo').val(), 'DD MMMM YYYY', 'id', true);
            var nominal = pba.to_backend_nominal(row.find('.dp-nominal').val());
            if (Number(nominal) <= 0 || !row.find('.dp-angsuran-ke').val()) {
                valid = false;
            }
            rows.push({
                // id: row.find('.dp-id').val() || '',
                // dp_is_pengurang_pokok: '',
                jenis_komponen: 'ANGSURAN',
                // deskripsi: '',
                nominal: nominal,
                angsuran_ke: row.find('.dp-angsuran-ke').val() || '',
                // jatuh_tempo: dueDate.isValid() ? dueDate.format('YYYY-MM-DD') : ''
            });
        });

        // console.log(rows.length)
        // return
        if (!rows.length || !valid) {
            bootbox.alert('Lengkapi nominal setiap baris DP dan nomor serta jatuh tempo untuk komponen angsuran.');
            return;
        }

        var params = {
            kode_pembiayaan: $('#key_kode_pembiayaan').val(),
            tanggal_dp: date && date.isValid() ? date.format('YYYY-MM-DD') : '',
            rows: rows
        };


        $.ajax({
            url: 'aset/PembiayaanAset/save_dp',
            type: 'POST',
            dataType: 'JSON',
            data: { params: params },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                if (response.status == 1) {
                    bootbox.alert(response.message, function () {
                        pba.load_dp(params.kode_pembiayaan);
                        pba.load_hitung_cicilan(params.kode_pembiayaan);
                        pba.hitung_cicilan();
                    });
                } else {
                    // button.prop('disabled', false);
                    bootbox.alert(response.message || 'Data gagal disimpan.');
                }
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan DP gagal diproses.');
            }
        });
    },

    delete_dp: function (element) {
        var button = $(element);
        bootbox.confirm('Hapus baris DP ini?', function (confirmed) {
            if (confirmed) {
                pba.tdp_request('aset/PembiayaanAset/delete_dp', { id: button.data('id') }, button);
            }
        });
    },

    format_rupiah: function (value) {
        var number = String(value || '').replace(/\D/g, '').slice(0, 13);
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },

    to_backend_nominal: function (value) {
        return String(value || '').replace(/\D/g, '') || '0';
    },

    update_tdp_rows: function () {
        var rows = $('#tdp-rows .tdp-row');
        rows.find('.tdp-remove-row').prop('disabled', rows.length <= 1);
    },

    update_tdp_total: function () {
        var total = 0;
        $('#tdp-rows .tdp-nominal').each(function () {
            total += Number(pba.to_backend_nominal($(this).val()));
        });
        $('#tdp-total').text(total.toLocaleString('id-ID', {
            maximumFractionDigits: 0
        }));
    },

    save_tdp: function () {
        var kode_pembiayaan = $("#key_kode_pembiayaan").val();
        // console.log(kode_pembiayaan)
        var dateValue   = $('#tgl_tdp').val();
        var date        = moment(dateValue, 'DD MMMM YYYY', 'id', true);
        if (!date.isValid()) {
            bootbox.alert('Tanggal TDP wajib dipilih.');
            return;
        }

        var rows  = [];

        $('#tdp-rows .tdp-row').each(function () {
            var description = $.trim($(this).find('.tdp-deskripsi').val() || '');
            var nominal = pba.to_backend_nominal($(this).find('.tdp-nominal').val());

            if (Number(nominal) <= 0) {
                return; 
            }

            rows.push({
                deskripsi: description,
                nominal: nominal
            });
        });

        //   console.log(rows.length)
        //   return

        if (rows.length < 1) {
            bootbox.alert('Nominal setiap baris TDP harus lebih dari 0.');
            return;
        }
        

        var params = {
            kode_pembiayaan: kode_pembiayaan,
            tanggal: date.format('YYYY-MM-DD'),
            is_pengurang_pokok: $('#tdp_pengurang_pokok').is(':checked') ? 1 : 0,
            rows: rows
        };

        $.ajax({
            url: 'aset/PembiayaanAset/save_tdp',
            type: 'POST',
            dataType: 'JSON',
            data: { params: params },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                if (response.status == 1) {
                    bootbox.alert(response.message, function () {
                        pba.load_tdp(kode_pembiayaan);
                        pba.load_hitung_cicilan(params.kode_pembiayaan);
                        pba.load_pelunasan(params.kode_pembiayaan)
                    });
                } else {
                    // button.prop('disabled', false);
                    bootbox.alert(response.message || 'Data gagal disimpan.');
                }
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },

    edit_tdp: function () {
        $("#form-tdp").find("input").prop("disabled", false);

        $("#button-tdp").html(`
            <button type="button" id="tdp-save"
                onclick="pba.save_tdp()"
                class="btn btn-primary">
                Simpan TDP
            </button>
            <button type="button"
                onclick="pba.batal_edit_tdp()"
                class="btn btn-default">
                Batal
            </button>
        `);

        $("#form-tdp .tdp-nominal")
            .off("input.inputRupiah")
            .on("input.inputRupiah", function () {
                pba.inputRupiah(this);
            });

            $("#tgl_tdp").css("background-color", "#fff");
            $("#tdp-add-row").prop("disabled", false);

            $(".tdp-remove-row").prop("disabled", false);
    },

    batal_edit_tdp: function () {
        var kode_pembiayaan = $("#key_kode_pembiayaan").val();
        $("#form-tdp").find("input").prop("disabled", true);

        $("#tdp-add-row").prop("disabled", true);
        $("#button-tdp").html(`
            <button type="button"
                onclick="pba.edit_tdp()"
                class="btn btn-warning">
                Edit TDP
            </button>
        `);

        $(".tdp-remove-row").prop("disabled", true);

        pba.load_tdp(kode_pembiayaan)
    },

    edit_dp: function () {
        $("#form-dp").find("input").prop("disabled", false);

        $("#button-dp").html(`
            <button type="button" id="dp-save"
                onclick="pba.save_dp()"
                class="btn btn-primary">
                Simpan DP
            </button>
            <button type="button"
                onclick="pba.batal_edit_dp()"
                class="btn btn-default">
                Batal
            </button>
        `);

        $("#form-dp .tdp-nominal")
            .off("input.inputRupiah")
            .on("input.inputRupiah", function () {
                pba.inputRupiah(this);
            });

            $("#tgl_dp").css("background-color", "#fff");
            $("#dp-add-row").prop("disabled", false);
            $("#dp-add-installment").prop("disabled", false);

            $(".dp-delete-row").prop("disabled", false);
    },

    batal_edit_dp: function () {
        $("#form-dp").find("input").prop("disabled", true);

        $("#tdp-add-row").prop("disabled", true);
        $("#button-dp").html(`
            <button type="button"
                onclick="pba.edit_dp()"
                class="btn btn-warning">
                Edit TDP
            </button>
        `);

        $(".tdp-remove-row").prop("disabled", true);
    },


    edit_leasing: function () {
        $("#detail-cicilan").find("input").prop("disabled", false);

        $("#button-cicilan").html(`
            <button type="button" id="dp-save"
                id="simpan-detail-cicilan" data-can-save="1"
                onclick="pba.simpan_detail_cicilan(this)"
                class="btn btn-primary">
                Simpan Detail Cicilan
            </button>
            <button type="button"
                onclick="pba.batal_edit_leasing()"
                class="btn btn-default">
                Batal
            </button>
        `);

        $("#detail-cicilan .tdp-nominal")
            .off("input.inputRupiah")
            .on("input.inputRupiah", function () {
                pba.inputRupiah(this);
            });

            $("#tgl_jt").css("background-color", "#fff");

    },

    batal_edit_leasing: function () {
        $("#detail-cicilan").find("input").prop("disabled", true);
        $("#button-cicilan").html(`
            <button type="button"
                onclick="pba.edit_leasing()"
                class="btn btn-warning">
                Edit Detail Cicilan
            </button>
        `);

        $("#tgl_jt").css("background-color", "#dcdbdb");
    },

    delete_tdp: function (element) {
        var button = $(element);
        bootbox.confirm('Hapus baris TDP ini?', function (confirmed) {
            if (confirmed) {
                pba.tdp_request('aset/PembiayaanAset/delete_tdp', {
                    id: button.data('id')
                }, button);
            }
        });
    },


    edit_pelunasan: function () {
        $("#detail-pelunasan").find("input").prop("disabled", false);

        $("#button-pelunasan").html(`
            <button type="button" id="tdp-save"
                onclick="pba.save_pelunasan()"
                class="btn btn-primary">
                Simpan TDP
            </button>
            <button type="button"
                onclick="pba.batal_edit_pelunasan()"
                class="btn btn-default">
                Batal
            </button>
        `);


        $("#tgl_pelunasan").css("background-color", "#fff");

        $("#pelunasan-delete").css("display", "block");

       
        $("#pelunasan-add-row").prop("disabled", false);

        $(".pelunasan-remove-row").prop("disabled", false);
    },

    batal_edit_pelunasan: function () {
        var kode_pembiayaan = $("#key_kode_pembiayaan").val();
        $("#detail-pelunasan").find("input").prop("disabled", true);

        $("#pelunasan-add-row").prop("disabled", true);
        $("#button-pelunasan").html(`
            <button type="button"
                onclick="pba.edit_tdp()"
                class="btn btn-warning">
                Edit TDP
            </button>
        `);

        $(".pelunasan-remove-row").prop("disabled", true);
        $("#pelunasan-delete").css("display", "none");

        // pba.load_pelunasan(kode_pembiayaan)
    },

    tdp_request: function (url, params, button) {
        button.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: { params: params },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                if (response.status == 1) {
                    bootbox.alert(response.message, function () {
                        window.location.reload(true);
                    });
                } else {
                    button.prop('disabled', false);
                    bootbox.alert(response.message || 'Data gagal disimpan.');
                }
            },
            error: function () {
                hideLoading();
                button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },


    load_tdp: function(kode_pembiayaan = null){
        let kode = $("#key_kode_pembiayaan").val() ?? kode_pembiayaan
        $.ajax({
            url: 'aset/PembiayaanAset/load_tdp',
            type: 'POST',
            dataType: 'html',
            data: { kode: kode },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (resp) {
                hideLoading();
                $('.load_tdp').html(resp)
                pba.init_datepickers()
                
            },
            error: function () {
                hideLoading();
                button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },

    load_dp: function(kode_pembiayaan = null){
        let supplier_name = $(".supplier_name").html();
        let kode = $("#key_kode_pembiayaan").val() ?? kode_pembiayaan
        $.ajax({
            url: 'aset/PembiayaanAset/load_dp',
            type: 'POST',
            dataType: 'html',
            data: { kode: kode },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (resp) {
                hideLoading();
                $('.load_dp').html(resp)
                pba.init_datepickers()

                $("#supplier-dp").val(supplier_name);
                
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },

    load_cicilan: function(kode_pembiayaan = null){
        let kode = $("#key_kode_pembiayaan").val() ?? kode_pembiayaan
        $.ajax({
            url: 'aset/PembiayaanAset/load_cicilan',
            type: 'POST',
            dataType: 'html',
            data: { kode: kode },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (resp) {
                hideLoading();
                $('.load_cicilan').html(resp)
                pba.init_datepickers()
                pba.init_cicilan();
                
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },

    load_hitung_cicilan: function(kode_pembiayaan = null){
        let kode = $("#key_kode_pembiayaan").val() ?? kode_pembiayaan
        $.ajax({
            url: 'aset/PembiayaanAset/load_hitung_cicilan',
            type: 'POST',
            dataType: 'html',
            data: { kode: kode },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (resp) {
                hideLoading();
                $('.cicilan-area').html(resp)
                pba.init_datepickers()
                
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },


    load_pelunasan: function(kode_pembiayaan = null){
        // let supplier_name = $(".supplier_name").html();
        let kode = $("#key_kode_pembiayaan").val() ?? kode_pembiayaan
        
        $.ajax({
            url: 'aset/PembiayaanAset/load_pelunasan',
            type: 'POST',
            dataType: 'html',
            data: { kode: kode },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (resp) {
                hideLoading();
                $('.load_pelunasan').html(resp)
                pba.init_datepickers()

                // $("#supplier-dp").val(supplier_name);
                
            },
            error: function () {
                hideLoading();
                // button.prop('disabled', false);
                bootbox.alert('Permintaan penyimpanan TDP gagal diproses.');
            }
        });
    },












    refresh_asset_summary: function () {
        var option = $('#kode_aset option:selected');
        var hasAsset = !!option.val();
        var assetSummary = $('#asset-summary');
        $('#summary-kode-aset').text(hasAsset ? (option.data('kode') || '-') : '-');
        $('#summary-kategori').text(option.data('kategori') || '-');
        $('#summary-unit').text(option.data('unit') || '-');
        $('#summary-harga').text(option.data('harga') ? option.data('harga') : '-');
        $('#summary-jenis').text(option.data('jenis') || '-');
        $('#summary-deskripsi').text(option.data('deskripsi') || '-');

        assetSummary.stop(true, true);
        if (hasAsset) {
            assetSummary.slideDown(250);
        } else {
            assetSummary.slideUp(200);
        }

        var leasingRequired = String(option.data('jenis') || '').toLowerCase() === 'leasing';
        $('#leasing-group').toggle(leasingRequired);
        $('#id_leasing').prop('required', leasingRequired);
        if (!leasingRequired) {
            $('#id_leasing').val('').trigger('change');
        }
    },

    open_action_tab: function (html) {
        $('#tab-action-content').html(html);
        pba.init_selects();
        pba.init_datepickers();
        pba.refresh_asset_summary();
        $('a[href="#action"]').trigger('click');
    },

    add_form: function () {
        $.get('aset/PembiayaanAset/add_form', function (html) {
            pba.open_action_tab(html);
        }, 'html').fail(function () {
            bootbox.alert('Form pembiayaan aset gagal dimuat.');
        });
    },

    edit_form: function (element) {
        $.get('aset/PembiayaanAset/edit_form', { id: $(element).data('id') }, function (html) {
            pba.open_action_tab(html);
        }, 'html').fail(function () {
            bootbox.alert('Data pembiayaan aset gagal dimuat.');
        });
    },

    // process_form: function (element) {
    //     var button = $(element);
    //     var id = encodeURIComponent(button.data('id'));
    //     var kodeJenis = String(button.data('kode-jenis-pembiayaan') || '').toUpperCase();
        
    //     var urlMap = {
    //         'PMB01': 'aset/PembiayaanAset/ProsesCash',
    //         'PMB02': 'aset/PembiayaanAset/ProsesTempo',
    //         'PMB03': 'aset/PembiayaanAset/ProsesLeasing'
    //     };
        
    //     var url = urlMap[kodeJenis] || ('aset/PembiayaanAset/ProsesLeasing?id=' + id);
        
    //     if (url.indexOf('?') === -1) {
    //         url += '?id=' + id;
    //     }
        
    //     window.open(url, '_blank');
    // },

    load_data: function () {
        $.ajax({
            url: 'aset/PembiayaanAset/list_data',
            type: 'POST',
            data: {
                id_kategori: $('#filter_kategori_aset').val() || '',
                search: $('#search_pembiayaan_aset').val() || ''
            },
            dataType: 'HTML',
            beforeSend: function () { showLoading(); },
            success: function (html) {
                $('.table-area').html(html);
                hideLoading();
            },
            error: function () {
                hideLoading();
                bootbox.alert('Daftar pembiayaan aset gagal dimuat.');
            }
        });
    },

    bind_search_filter: function () {
        $(document).off('input.pbaSearch', '#search_pembiayaan_aset')
            .on('input.pbaSearch', '#search_pembiayaan_aset', function () {
                clearTimeout(pba.searchTimer);
                pba.searchTimer = setTimeout(function () {
                    pba.load_data();
                }, 300);
            });
    },

    filter_data: function (event) {
        if (event) {
            event.preventDefault();
        }
        pba.load_data();
    },

    reset_filter: function (event) {
        if (event) {
            event.preventDefault();
        }
        $('#filter_kategori_aset').val('').trigger('change');
        $('#search_pembiayaan_aset').val('');
        pba.load_data();
    },

    submit: function (url, params, confirmation) {
        bootbox.confirm(confirmation, function (confirmed) {
            if (!confirmed) {
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: { params: params },
                beforeSend: function () { showLoading(); },
                success: function (response) {
                    hideLoading();
                    if (response.status == 1) {
                        bootbox.alert(response.message, function () {
                            window.location.reload(true);
                        });
                    } else {
                        bootbox.alert(response.message);
                    }
                },
                error: function () {
                    hideLoading();
                    bootbox.alert('Permintaan pembiayaan aset gagal diproses.');
                }
            });
        });
    },

    get_form_params: function () {
        var params = {
            kode_aset: $('#kode_aset').val() || '',
            id_supplier: $('#id_supplier').val() || '',
            id_leasing: $('#id_leasing').val() || ''
        };
        if (!params.kode_aset) {
            bootbox.alert('Aset wajib dipilih.');
            return null;
        }
        if (!params.id_supplier) {
            bootbox.alert('Supplier wajib dipilih.');
            return null;
        }
        if ($('#leasing-group').is(':visible') && !params.id_leasing) {
            bootbox.alert('Leasing wajib dipilih.');
            return null;
        }
        return params;
    },

    save_data: function () {
        var params = pba.get_form_params();
        if (params) {
            pba.submit('aset/PembiayaanAset/save_data', params, 'Simpan pembiayaan aset ini?');
        }
    },

    edit_data: function () {
        var params = pba.get_form_params();
        if (params) {
            params.id = $('#id_pembiayaan_aset').val();
            pba.submit('aset/PembiayaanAset/edit_data', params, 'Simpan perubahan pembiayaan aset ini?');
        }
    },

    delete_data: function (element) {
        pba.submit('aset/PembiayaanAset/delete_data', $(element).data('id'), 'Hapus data pembiayaan aset ini?');
    },


    hitung_ulang_cicilan: function () {
        var tenor = parseInt($('#edit_tenor').val()) || 0;
        var bunga = parseFloat($('#edit_bunga').val()) || 0;

        if (tenor < 1 || tenor > 600) {
            $('#edit_tenor_alert').text('Tenor harus antara 1-600 bulan.').removeClass('hide');
            return;
        }
        if (bunga < 0 || bunga > 100) {
            $('#edit_bunga_alert').text('Bunga harus antara 0-100%.').removeClass('hide');
            return;
        }
        $('#edit_tenor_alert, #edit_bunga_alert').addClass('hide').text('');

        var panel = $('#detail-cicilan');
        if (!panel.length) {
            bootbox.alert('Data pembiayaan tidak ditemukan.');
            return;
        }

        var cents = function (value) {
            var amount = Number(value);
            return Number.isFinite(amount) ? Math.round(amount * 100) : 0;
        };
        var formatMoney = function (valueInCents) {
            return (valueInCents / 100).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        var priceCents          = cents(panel.attr('data-harga-beli'));
        var tdpCents            = cents(panel.attr('data-tdp-pengurang'));
        var downPaymentCents    = cents(panel.attr('data-dp-uang-muka'));
        // var principalCents = priceCents - tdpCents - downPaymentCents;
        var principalCents = (priceCents - downPaymentCents) + biayaPenambahCents - tdpCents;

        if (principalCents < 0) {
            $('#edit_pokok_hutang').text('0,00');
            $('#edit_total_bunga, #edit_total_ar, #edit_angsuran_bulanan').text('0,00');
            $('#edit_selisih_pembulatan').empty();
            $('#edit_detail_cicilan_body').empty().append(
                $('<tr>').append(
                    $('<td>').attr('colspan', 5).addClass('text-center text-muted').text('Total TDP dan uang muka melebihi harga beli aset.')
                )
            );
            return;
        }

        var totalInterestCents = Math.round(principalCents * bunga * tenor / 1200);
        var totalArCents = principalCents + totalInterestCents;
        var baseInstallmentCents = Math.ceil(totalArCents / tenor / 10000) * 10000;
        var roundingDifferenceCents = baseInstallmentCents * tenor - totalArCents;
        var lastInstallmentCents = baseInstallmentCents - roundingDifferenceCents;

        $('#edit_pokok_hutang').text(formatMoney(principalCents));
        $('#edit_total_bunga').text(formatMoney(totalInterestCents));
        $('#edit_total_ar').text(formatMoney(totalArCents));
        $('#edit_angsuran_bulanan').text(formatMoney(baseInstallmentCents));
        $('#edit_tenor_label').text(tenor);

        if (roundingDifferenceCents === 0) {
            $('#edit_selisih_pembulatan').empty();
        } else {
            $('#edit_selisih_pembulatan').text('Selisih pembulatan terhadap A/R: ' + formatMoney(roundingDifferenceCents) + '.');
        }

        var dueDateBase = panel.attr('data-tanggal-dasar');
        var monthOffset = 0;
        if (!dueDateBase) {
            dueDateBase = panel.attr('data-tanggal-pembiayaan');
            monthOffset = 1;
        }
        var startDate = moment(dueDateBase, 'YYYY-MM-DD', true);

        var tbody = $('#edit_detail_cicilan_body');
        tbody.empty();

        if (!dueDateBase || !startDate.isValid()) {
            tbody.append(
                $('<tr>').append(
                    $('<td>').attr('colspan', 5).addClass('text-center text-muted').text('Tanggal dasar cicilan tidak tersedia.')
                )
            );
            return;
        }

        for (var installment = 1; installment <= tenor; installment++) {
            var dueDate = startDate.clone().add(monthOffset + installment - 1, 'months');
            var nominalCents = installment === tenor ? lastInstallmentCents : baseInstallmentCents;

            var jenisCicilan = 'Cicilan';
            var labelJenis = 'label-default';

            var row = $('<tr>');
            row.append($('<td>').addClass('text-center').text(installment));
            row.append($('<td>').text(dueDate.format('DD MMMM YYYY')));
            row.append($('<td>').addClass('text-center').append($('<span>').addClass('label ' + labelJenis).text(jenisCicilan)));
            row.append($('<td>').addClass('text-right').text(formatMoney(nominalCents)));
            row.append($('<td>').addClass('text-center').append($('<span>').addClass('label label-warning').text('Pending')));
            tbody.append(row);
        }
    },


    simpan_perubahan_cicilan: function () {
        var tenor = parseInt($('#edit_tenor').val()) || 0;
        var bunga = parseFloat($('#edit_bunga').val()) || 0;

        if (tenor < 1 || tenor > 600) {
            bootbox.alert('Tenor harus antara 1-600 bulan.');
            return;
        }
        if (bunga < 0 || bunga > 100) {
            bootbox.alert('Bunga harus antara 0-100%.');
            return;
        }

        var kodePembiayaan = $('#kode_pembiayaan_tdp').val() || $('#kode_pembiayaan_dp').val();

        $.ajax({
            url: 'aset/PembiayaanAset/update_detail_cicilan',
            type: 'POST',
            dataType: 'JSON',
            data: { 
                params: { 
                    kode_pembiayaan: kodePembiayaan,
                    tenor_bulan: tenor,
                    bunga_flat_persen: bunga
                } 
            },
            beforeSend: function () { showLoading(); },
            success: function (response) {
                hideLoading();
                if (response.status == 1) {
        
                    $('.bootbox').modal('hide');
                    
                    bootbox.alert({
                        title: 'Berhasil',
                        message: 'Cicilan berhasil diubah.',
                        callback: function () {
                            
                            window.location.reload();
                        }
                    });
                } else {
                    bootbox.alert(response.message || 'Gagal mengupdate cicilan.');
                }
            },
            error: function () {
                hideLoading();
                bootbox.alert('Gagal menghubungi server.');
            }
        });
    },



    edit_tempo: function () {
        var kodePembiayaan = $('#kode_pembiayaan_tempo').val();
        if (!kodePembiayaan) return;

        $.ajax({
            url: 'aset/PembiayaanAset/get_detail_tempo',
            type: 'POST',
            dataType: 'html',
            data: { params: { kode_pembiayaan: kodePembiayaan } },
            success: function (html) {
                
                var dialog = bootbox.dialog({
                    title: 'Edit Rencana Tempo',
                    message: html,
                    size: 'large',
                    closeButton: true
                });
            },
            error: function () {
                // dialog.hide();
                bootbox.alert('Gagal memuat data tempo.');
            }
        });
    },

        // Hitung Total di dalam Bootbox Modal
    hitung_edit_tempo: function () {
        var hargaBeli = parseFloat($('#detail-tempo').data('harga-beli')) || 0;
        var tdpTotal = parseFloat($('#detail-tempo').data('tdp-total')) || 0;
        
        var biayaLainStr = $('#edit_biaya_lain').val().replace(/\./g, '');
        var biayaLain = parseFloat(biayaLainStr) || 0;
        
        var totalPembelian = hargaBeli + biayaLain;
        var sisa = totalPembelian - tdpTotal;
        var totalTermin = 0;

        $('.edit-tempo-nominal').each(function() {
            var val = $(this).val().replace(/\./g, '');
            totalTermin += parseFloat(val) || 0;
        });

        // Format dengan titik pemisah ribuan
        var formatRupiah = function(num) {
            return num.toLocaleString('id-ID');
        };

        $('#edit_display_total_pembelian').text(formatRupiah(totalPembelian));
        $('#edit_display_tdp').text('(' + formatRupiah(tdpTotal) + ')');
        $('#edit_display_sisa').text(formatRupiah(sisa));
        $('#edit_display_total_termin').text(formatRupiah(totalTermin));

        var selisih = sisa - totalTermin;
        if (selisih !== 0) {
            $('#edit_selisih_tempo').text('Selisih: ' + formatRupiah(selisih)).removeClass('text-success').addClass('text-danger');
        } else {
            $('#edit_selisih_tempo').text('✓ Pas').removeClass('text-danger').addClass('text-success');
        }

        // Disable tombol simpan jika belum pas
        var isMatch = (selisih === 0) && (totalTermin > 0);
        $('.bootbox').find('button.btn-primary').prop('disabled', !isMatch);
    },

    generate_edit_tempo_rows: function (loadExisting = true) {
        var jumlahTermin = parseInt($('#edit_jumlah_termin').val()) || 0;
        var container = $('#edit_tempo_rows');
        container.empty();

        if (jumlahTermin < 1) return;

        var details = [];
        if (loadExisting) {
            try {
                details = JSON.parse($('#edit_tempo_data').html()) || [];
            } catch (e) { details = []; }
        }

        var formatTanggal = function(dateStr) {
            if (!dateStr) return '';
            var bulanID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            var parts = dateStr.split('-');
            if (parts.length === 3) {
                return parseInt(parts[2]) + ' ' + bulanID[parseInt(parts[1]) - 1] + ' ' + parts[0];
            }
            return dateStr;
        };

        var formatNominal = function(num) {
            if (!num) return '0';
            return parseFloat(num).toLocaleString('id-ID');
        };

        for (var i = 1; i <= jumlahTermin; i++) {
            var existing = details[i - 1] || {};
            var jatuhTempo = formatTanggal(existing.jatuh_tempo || '');
            var nominal = formatNominal(existing.nominal || 0);

            var row = $(
                '<div class="row tempo-edit-row" style="margin-bottom:8px;" data-row="' + i + '">' +
                    '<div class="col-xs-1" style="padding-top:7px;"><strong>' + i + '</strong></div>' +
                    '<div class="col-xs-5">' +
                        '<div class="input-group date tempo-edit-picker">' +
                            '<input type="text" class="form-control edit-tempo-jatuh-tempo" value="' + jatuhTempo + '">' +
                            '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-xs-5">' +
                        '<div class="input-group">' +
                            '<span class="input-group-addon">Rp</span>' +
                            '<input type="text" class="form-control text-right edit-tempo-nominal" value="' + nominal + '" onkeyup="pba.hitung_edit_tempo()">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-xs-1"></div>' +
                '</div>'
            );

            container.append(row);

            // Init datepicker TANPA minDate (bebas pilih)
            row.find('.tempo-edit-picker').datetimepicker({
                locale: 'id',
                format: 'DD MMMM YYYY',
                useCurrent: false
            });

            // Format nominal saat ketik
            row.find('.edit-tempo-nominal').on('keyup', function() {
                var val = $(this).val().replace(/\D/g, '');
                $(this).val(formatNominal(val));
                pba.hitung_edit_tempo();
            });
        }

        // Validasi urutan tanggal SETELAH user pilih
        $(document).off('dp.change.tempo').on('dp.change.tempo', '.tempo-edit-picker', function(e) {
            if (!e.date) return;

            var currentRow = $(this).closest('.tempo-edit-row');
            var currentIdx = parseInt(currentRow.data('row'));
            var inputField = $(this).find('.edit-tempo-jatuh-tempo');
            var selectedDate = e.date;

            if (currentIdx > 1) {
                var prevRow = $('.tempo-edit-row[data-row="' + (currentIdx - 1) + '"]');
                var prevDateStr = prevRow.find('.edit-tempo-jatuh-tempo').val();
                var prevDate = moment(prevDateStr, 'DD MMMM YYYY', 'id', true);

                if (prevDate.isValid() && selectedDate.isBefore(prevDate, 'day')) {
               
                    setTimeout(function() {
                        inputField.val('');
                        bootbox.alert('Tanggal Termin ' + currentIdx + ' tidak boleh lebih kecil dari Termin ' + (currentIdx - 1) + ' (' + prevDateStr + ')!');
                    }, 100);
                    pba.hitung_edit_tempo();
                    return;
                }
            }

            pba.hitung_edit_tempo();
        });

        pba.hitung_edit_tempo();
    },
    
    update_rencana_tempo: function () {
        var jumlahTermin = Number($('#edit_jumlah_termin').val() || 0);
        if (jumlahTermin < 1) {
            bootbox.alert('Jumlah termin harus diisi.');
            return;
        }

        var rows = [];
        var valid = true;

        $('#edit_tempo_rows .row').each(function (index) {
        
            var tanggalStr = $(this).find('.edit-tempo-jatuh-tempo').val();
            var tanggal = moment(tanggalStr, 'DD MMMM YYYY', 'id', true);
            var nominalStr = $(this).find('.edit-tempo-nominal').val().replace(/\./g, '');
            var nominal = nominalStr;

            if (!tanggal.isValid()) {
                bootbox.alert('Jatuh tempo termin ke-' + (index + 1) + ' tidak valid.');
                valid = false;
                return false;
            }
            if (Number(nominal) <= 0) {
                bootbox.alert('Nominal termin ke-' + (index + 1) + ' harus lebih dari 0.');
                valid = false;
                return false;
            }

            rows.push({
                jatuh_tempo: tanggal.format('YYYY-MM-DD'),
                nominal: nominal
            });
        });

        if (!valid) {
            return;
        }

        if (rows.length !== jumlahTermin) {
            bootbox.alert('Jumlah baris termin harus sama dengan jumlah termin (' + jumlahTermin + ').');
            return;
        }

        var selisihText = $('#edit_selisih_tempo').text();
        if (selisihText.indexOf('Selisih') !== -1) {
            bootbox.alert('Total nominal termin belum sesuai dengan sisa dijadwalkan. Mohon perbaiki dulu.');
            return;
        }

        var params = {
            kode_pembiayaan: $('#kode_pembiayaan_tempo').val(), 
            harga_beli: String($('#detail-tempo').data('harga-beli')), 
            biaya_lain: $('#edit_biaya_lain').val().replace(/\./g, ''),
            jumlah_termin: String(jumlahTermin),
            rows: rows
        };

        
        bootbox.confirm({
            title: 'Konfirmasi Update',
            message: 'Anda yakin ingin memperbarui rencana tempo?<br><span class="text-danger">Data lama akan diganti dengan data baru.</span>',
            buttons: {
                cancel: { label: 'Batal', className: 'btn-default' },
                confirm: { label: 'Ya, Update', className: 'btn-warning' }
            },
            callback: function (result) {
                if (result) {
                    pba.tdp_request('aset/PembiayaanAset/update_rencana_tempo', params, $('#btn_update_tempo'));
                }
            }
        });
    },

    formatInputRupiah: function (element) {
        let val = $(element).val() || '';
        val = val.replace(/\D/g, '');

        $(element).val(
            val.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
        );
    },


    tdpRemoveRow: function (element) {
        $(element).closest('.tdp-row').remove();
        pba.hitung_tdp_total();
    },

    inputRupiah: function (element) {
        var val = $(element).val() || '';
        val = val.replace(/\D/g, '');

        $(element).val(
            val.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
        );

        pba.hitung_tdp_total();
        pba.update_dp_total();
    },

    tdpAddRow: function () {
        var newRow = `
            <div class="row tdp-row" style="margin-bottom:8px;">
                <div class="col-xs-7">
                    <input type="text"
                        class="form-control tdp-deskripsi"
                        value=""
                        maxlength="255"
                        placeholder="Deskripsi">
                </div>

                <div class="col-xs-4">
                    <div class="input-group">
                        <span class="input-group-addon">Rp</span>
                        <input type="text"
                            class="form-control text-right tdp-nominal"
                            oninput="pba.inputRupiah(this)"
                            placeholder="0"
                            inputmode="numeric">
                    </div>
                </div>

                <div class="col-xs-1">
                    <button type="button"
                        class="btn btn-danger btn-sm tdp-remove" onclick="pba.tdpRemoveRow(this)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        $('#tdp-rows').append(newRow);
        pba.hitung_tdp_total();
    },

    hitung_tdp_total: function() {
        var total = 0;
        
        $('.tdp-row').each(function() {
            var val = $(this).find('.tdp-nominal').val().replace(/\D/g, '');
            total += parseFloat(val) || 0;
        });

        $('#tdp-total').text(total.toLocaleString('id-ID'));
    },


    add_tdp: function (element, event) {
        if (event) {
            event.preventDefault();
        }

        var $panel = $(element).closest('.installment-panel');
        var $bodyLama = $panel.children('.panel-body').first();
        var $template = $('#body_tdp');

        // Hindari menambahkan form TDP lebih dari satu kali
        if ($panel.find('.tdp-clone').length) {
            return;
        }

        // Clone template
        var $clone = $template.clone(false, false);

        $clone
            .removeAttr('id')
            .removeAttr('style')
            .addClass('tdp-clone');

        // Sesuaikan ID tanggal pada form hasil clone
        var $datePicker = $clone.find('#tgl_tdp_clone_picker');
        var $dateInput = $clone.find('#tgl_tdp_clone');

        $datePicker.attr('id', 'tgl_tdp_picker');
        $dateInput.attr('id', 'tgl_tdp');

        // Simpan body lama untuk dimunculkan kembali jika batal
        $bodyLama.hide();

        // Masukkan form baru ke panel yang sama
        $panel.append($clone);

        // Inisialisasi datetimepicker pada form hasil clone
        if ($.fn.datetimepicker) {
            moment.locale('id');

            var value = $dateInput.val();
            var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);

            if (value && date.isValid()) {
                $dateInput.val(date.format('DD MMMM YYYY'));
            }

            $datePicker.datetimepicker({
                locale: 'id',
                format: 'DD MMMM YYYY',
                useCurrent: false
            });
        }
    },


    batal_tdp: function () {
        // Cari panel TDP yang sedang aktif
        var $panel = $('.installment-panel').has('.tdp-clone');

        if (!$panel.length) {
            return;
        }

        // Hapus form TDP hasil clone
        $panel.find('.tdp-clone').remove();

        // Tampilkan kembali body panel lama
        $panel.children('.panel-body').first().show();
    },



    
};

window.pba = pba;

$(document).ready(function () {
    pba.start_up();
});
