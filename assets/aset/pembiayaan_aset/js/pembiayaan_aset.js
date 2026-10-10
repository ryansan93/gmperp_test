var pba = {
    searchTimer: null,

    start_up: function () {
        if ($('#form-pelunasan').length) {
            pba.init_datepickers();
            pba.init_tdp();
            pba.init_pelunasan();
        } else if ($('#detail-tempo').length) {
            pba.init_datepickers();
            pba.init_tdp();
            pba.init_tempo();
        } else if ($('#form-tdp').length) {
            pba.init_datepickers();
            pba.init_tdp();
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
        $('#tgl_tdp, #tgl_dp, #tgl_pelunasan, #tgl_modal_tdp').each(function () {
            var value = $(this).val();
            var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);
            if (value && date.isValid()) {
                $(this).val(date.format('DD MMMM YYYY'));
            }
        });

        $('#tgl_tdp_picker, #tgl_dp_picker, #tgl_pelunasan_picker, #tgl_modal_tdp_picker').each(function () {
            var picker = $(this);
            if (!picker.data('DateTimePicker')) {
                picker.datetimepicker({
                    locale: 'id',
                    format: 'DD MMMM YYYY',
                    useCurrent: false
                });
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

    init_tdp: function () {
        $(document)
            .off('click.tdpAdd', '#tdp-add-row')
            .on('click.tdpAdd', '#tdp-add-row', function () {
                var row = $('<div class="row tdp-row" style="margin-bottom:8px;">' +
                    '<div class="col-xs-7"><input type="text" class="form-control tdp-deskripsi" maxlength="255" placeholder="Deskripsi"></div>' +
                    '<div class="col-xs-4"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right tdp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
                    '<div class="col-xs-1"><button type="button" class="btn btn-danger btn-sm tdp-remove-row" title="Hapus baris"><i class="fa fa-times"></i></button></div>' +
                    '</div>');
                $('#tdp-rows').append(row);
                pba.update_tdp_rows();
            })
            .off('click.tdpRemove', '.tdp-remove-row')
            .on('click.tdpRemove', '.tdp-remove-row', function () {
                $(this).closest('.tdp-row').remove();
                pba.update_tdp_rows();
            })
            .off('input.pbaNominal', '.tdp-nominal')
            .on('input.pbaNominal', '.tdp-nominal', function () {
                $(this).val(pba.format_rupiah($(this).val()));
                pba.update_tdp_total();
            })
            .off('submit.tdpSave', '#form-tdp')
            .on('submit.tdpSave', '#form-tdp', function (event) {
                event.preventDefault();
                pba.save_tdp();
            });

        $('.tdp-nominal, .dp-nominal').each(function () {
            $(this).val(pba.format_rupiah($(this).val()));
        });
        pba.update_tdp_rows();
        pba.update_tdp_total();
    },

    init_pelunasan: function () {
        var panel = $('#detail-pelunasan');
        if (!panel.length) {
            return;
        }

        var canMutate = panel.attr('data-can-mutate') === '1';

        $(document)
            .off('click.pelunasanAdd', '#pelunasan-add-row')
            .on('click.pelunasanAdd', '#pelunasan-add-row', function () {
                if (!canMutate) {
                    return;
                }
                var row = $('<div class="row pelunasan-row" style="margin-bottom:8px;">' +
                    '<div class="col-xs-7"><input type="text" class="form-control pelunasan-deskripsi" maxlength="255" placeholder="Deskripsi"></div>' +
                    '<div class="col-xs-4"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right pelunasan-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
                    '<div class="col-xs-1"><button type="button" class="btn btn-danger btn-sm pelunasan-remove-row" title="Hapus baris"><i class="fa fa-times"></i></button></div>' +
                    '</div>');
                $('#pelunasan-rows').append(row);
                pba.update_pelunasan_total();
            })
            .off('submit.pelunasanSave', '#form-pelunasan')
            .on('submit.pelunasanSave', '#form-pelunasan', function (event) {
                event.preventDefault();
                pba.save_pelunasan();
            })
            .off('click.pelunasanDelete', '#pelunasan-delete')
            .on('click.pelunasanDelete', '#pelunasan-delete', function () {
                pba.delete_pelunasan(this);
            })
            .off('click.pelunasanRemove', '.pelunasan-remove-row')
            .on('click.pelunasanRemove', '.pelunasan-remove-row', function () {
                if (!canMutate) {
                    return;
                }
                $(this).closest('.pelunasan-row').remove();
                pba.update_pelunasan_rows();
                pba.update_pelunasan_total();
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


    init_tempo: function () {
        var panel = $('#detail-tempo');
        if (!panel.length) {
            return;
        }

        var canMutate = panel.attr('data-can-mutate') === '1';

        // Auto-generate baris saat tombol "+ Tambah baris" diklik
        $(document)
            .off('click.tempoGenerate', '#btn_tambah_termin')
            .on('click.tempoGenerate', '#btn_tambah_termin', function () {
                if (!canMutate) {
                    return;
                }
                pba.generate_tempo_rows();
            })
            // Auto-generate juga saat user selesai mengetik jumlah termin (blur/change)
            .off('change.tempoTermin', '#jumlah_termin')
            .on('change.tempoTermin', '#jumlah_termin', function () {
                if (canMutate) {
                    pba.generate_tempo_rows();
                }
            })
            // Hapus baris
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

    save_pelunasan: function () {
        var panel = $('#detail-pelunasan');
        if (panel.attr('data-can-mutate') !== '1') {
            return;
        }

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
            id: $('#id_pelunasan').val() || '',
            harga_beli: $('#harga_beli').val() || '',
            kode_pembiayaan: $('#kode_pembiayaan_pelunasan').val(),
            tanggal_pelunasan: date.format('YYYY-MM-DD'),
            // total_pembelian : $('#total-pembelian').html(),
            // tdp : $('#dikurangi-tanda-jadi').html(),
            // diajukan : $('#diajukan').html(),
            rows: rows  
        };

        pba.pelunasan_request('aset/PembiayaanAset/save_pelunasan', params, $('#pelunasan-save'));
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

    init_dp: function () {
        $(document)
            .off('click.dpAdd', '#dp-add-row')
            .on('click.dpAdd', '#dp-add-row', function () {
                var row = $('<div class="row dp-row" style="margin-bottom:8px;">' +
                    '<input type="hidden" class="dp-id" value="">' +
                    '<div class="col-xs-8"><input type="text" class="form-control dp-komponen" placeholder="Jenis komponen (contoh: UANG_MUKA)"></div>' +
                    '<div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
                    '<div class="col-xs-1 text-right"><button type="button" class="btn btn-danger btn-sm dp-remove-row" title="Hapus baris"><i class="fa fa-times"></i></button></div>');
                $('#dp-rows').append(row);
            })
            .off('click.dpAddInstallment', '#dp-add-installment')
            .on('click.dpAddInstallment', '#dp-add-installment', function () {
                var installmentNumber = $('#dp-installment-rows .dp-installment-row').length + 1;
                var row = $('<div class="row dp-installment-row" style="margin-bottom:8px;">' +
                    '<input type="hidden" class="dp-id" value="">' +
                    '<div class="col-xs-2"><label style="padding-top:7px;">Angsuran ke</label></div>' +
                    '<div class="col-xs-2"><input type="number" class="form-control dp-angsuran-ke" min="1" value="' + installmentNumber + '" placeholder="Ke"></div>' +
                    '<div class="col-xs-3"><div class="input-group date dp-jatuh-tempo-picker"><input type="text" class="form-control dp-jatuh-tempo" autocomplete="off" placeholder="Jatuh tempo"><span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>' +
                    '<div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>' +
                    '<div class="col-xs-2 text-right"><button type="button" class="btn btn-danger btn-sm dp-remove-row" title="Hapus angsuran"><i class="fa fa-times"></i></button></div></div>');
                $('#dp-installment-rows').append(row);
                if ($.fn.datetimepicker) {
                    row.find('.dp-jatuh-tempo-picker').datetimepicker({
                        locale: 'id',
                        format: 'DD MMMM YYYY',
                        useCurrent: false
                    });
                }
            })
            .off('click.dpRemove', '.dp-remove-row')
            .on('click.dpRemove', '.dp-remove-row', function () {
                $(this).closest('.dp-row, .dp-installment-row').remove();
                pba.update_dp_total();
            })
            .off('input.dpNominal', '.dp-nominal')
            .on('input.dpNominal', '.dp-nominal', function () {
                $(this).val(pba.format_rupiah($(this).val()));
                pba.update_dp_total();
            })
            .off('click.dpDueDate', '.dp-jatuh-tempo-picker .input-group-addon')
            .on('click.dpDueDate', '.dp-jatuh-tempo-picker .input-group-addon', function () {
                var picker = $(this).closest('.dp-jatuh-tempo-picker');
                if (picker.data('DateTimePicker')) {
                    picker.datetimepicker('show');
                }
            })
            .off('input.dpComponent', '.dp-komponen')
            .on('input.dpComponent', '.dp-komponen', function () {
                pba.update_dp_total();
            })
            .off('click.dpDelete', '.dp-delete-row')
            .on('click.dpDelete', '.dp-delete-row', function () {
                pba.delete_dp(this);
            })
            .off('submit.dpSave', '#form-dp')
            .on('submit.dpSave', '#form-dp', function (event) {
                event.preventDefault();
                pba.save_dp();
            });

        $('.dp-nominal').each(function () {
            $(this).val(pba.format_rupiah($(this).val()));
        });
        $('.dp-jatuh-tempo').each(function () {
            var value = $(this).val();
            var date = moment(value, ['YYYY-MM-DD', 'DD MMMM YYYY'], true);
            if (value && date.isValid()) {
                $(this).val(date.format('DD MMMM YYYY'));
            }
        });
        if ($.fn.datetimepicker) {
            $('.dp-jatuh-tempo-picker').each(function () {
                if (!$(this).data('DateTimePicker')) {
                    $(this).datetimepicker({
                        locale: 'id',
                        format: 'DD MMMM YYYY',
                        useCurrent: false
                    });
                }
            });
        }
        $('.dp-komponen').trigger('change');
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
        $('#tenor, #bunga_flat')
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

    hitung_cicilan: function () {
        var panel = $('#detail-cicilan');
        if (!panel.length) return;

        var cents = function (value) {
            var amount = Number(value);
            return Number.isFinite(amount) ? Math.round(amount * 100) : 0;
        };
        
        var formatMoney = function (valueInCents) {
            return Math.round(valueInCents / 100).toLocaleString('id-ID');
        };

        var priceCents = cents(panel.attr('data-harga-beli'));
        var tdpCents = cents(panel.attr('data-tdp-pengurang'));
        var downPaymentCents = cents(panel.attr('data-dp-uang-muka'));
        var principalCents = priceCents - downPaymentCents - tdpCents;
        var tbody = $('#jadwal-cicilan');

        $('#cicilan-harga-beli').text(formatMoney(priceCents));
        $('#cicilan-tdp-pengurang').text('(' + formatMoney(tdpCents) + ')');
        $('#cicilan-dp-uang-muka').text('(' + formatMoney(downPaymentCents) + ')');
        $('#cicilan-pokok-hutang').text(formatMoney(Math.max(principalCents, 0)));

        var showMessage = function (msg) {
            tbody.empty().append($('<tr>').append($('<td>').attr('colspan', 8).addClass('text-center text-muted').text(msg)));
        };

        if (principalCents < 0) {
            showMessage('Total TDP dan uang muka melebihi harga beli aset.');
            return;
        }

        var tenor = Number($('#tenor').val());
        var annualRate = Number($('#bunga_flat').val());
        
        if (!tenor || tenor < 1) {
            showMessage('Jadwal cicilan akan tampil setelah tenor diisi.');
            return;
        }
        
        if (!annualRate || isNaN(annualRate) || annualRate <= 0) {
            showMessage('Jadwal cicilan akan tampil setelah bunga flat diisi.');
            return;
        }

        // ===== RUMUS ANUITAS =====
        var monthlyRate = (annualRate / 100) / 12;
        var totalMonths = tenor;
        
        var baseInstallmentCents = Math.round(
            principalCents * (monthlyRate / (1 - Math.pow(1 + monthlyRate, -totalMonths)))
        );

        var totalPaymentCents = baseInstallmentCents * tenor;
        var totalInterestCents = totalPaymentCents - principalCents;

        $('#cicilan-nominal-bulanan').text(formatMoney(baseInstallmentCents));
        $('#cicilan-tenor-label').text(tenor);
        $('#cicilan-total-bunga').text(formatMoney(totalInterestCents));
        $('#cicilan-total-ar').text(formatMoney(totalPaymentCents));
        $('#cicilan-selisih-pembulatan').text('');
        $('#simpan-detail-cicilan').prop('disabled', $('#simpan-detail-cicilan').attr('data-can-save') !== '1');

        // ===== GENERATE TABEL =====
        tbody.empty();
        
        var currentBalanceCents = principalCents;
        var totalBungaTerakumulasi = 0;
        
        var startDate = moment(panel.attr('data-tanggal-dasar') || panel.attr('data-tanggal-pembiayaan'), 'YYYY-MM-DD', true);
        if (!startDate.isValid()) startDate = moment();

        for (var i = 1; i <= tenor; i++) {
            var dueDate = startDate.clone().add(i, 'months');
            
            var bungaCents = Math.round(currentBalanceCents * monthlyRate);
            var pokokCents = baseInstallmentCents - bungaCents;
            
            if (i === tenor) {
                pokokCents = currentBalanceCents;
                bungaCents = baseInstallmentCents - pokokCents;
            }
            
            currentBalanceCents -= pokokCents;
            if (currentBalanceCents < 0) currentBalanceCents = 0;
            
            totalBungaTerakumulasi += bungaCents;

            var row = $('<tr>');
            row.append($('<td class="text-center">').text(i));                                          
            row.append($('<td class="text-left">').text(dueDate.format('DD MMMM YYYY')));               
            row.append($('<td class="text-center">').html('<span class="label label-default">CICILAN</span>')); 
            row.append($('<td class="text-right">').text(formatMoney(pokokCents)));                      
            row.append($('<td class="text-right">').text(formatMoney(bungaCents)));                      
            row.append($('<td class="text-right">').text(formatMoney(baseInstallmentCents)));            
            row.append($('<td class="text-right">').text(formatMoney(currentBalanceCents)));             
            row.append($('<td class="text-center">').html('<span class="label label-warning">Pending</span>')); 
            tbody.append(row);
        }
    },

    simpan_detail_cicilan: function (buttonElement) {
        var button = $(buttonElement);
        var panel = $('#detail-cicilan');
        var tenor = $('#tenor').val();
        var bunga = $('#bunga_flat').val();
        
        if (button.attr('data-can-save') !== '1' || button.prop('disabled')) {
            return;
        }
        if (!tenor || !bunga || !$('#jadwal-cicilan tr').length || $('#jadwal-cicilan td[colspan]').length) {
            bootbox.alert('Lengkapi tenor dan bunga, lalu pastikan jadwal cicilan sudah terbentuk.');
            return;
        }

        var getCleanInteger = function(selector) {
            var rawText = $(selector).text();
            var withoutThousands = String(rawText).replace(/\./g, '');
            var withDotDecimal = withoutThousands.replace(',', '.');
            
            // 3. Parse sebagai float, lalu bulatkan ke integer
            var value = parseFloat(withDotDecimal) || 0;
            
            return String(Math.round(value));
        };

        let dp = parseInt($('#cicilan-dp-uang-muka').attr('dp'));

        var params = {
            kode_pembiayaan: $('#kode_pembiayaan_dp').val() || $('#kode_pembiayaan_tdp').val(),
            tenor: tenor,
            bunga: bunga,
            harga_beli: getCleanInteger('#cicilan-harga-beli'),
            tdp_pengurang: getCleanInteger('#cicilan-tdp-pengurang'),
            dp: dp,
            pokok_hutang: getCleanInteger('#cicilan-pokok-hutang'),
            biaya_prepaid: getCleanInteger('#cicilan-biaya-prepaid'),
            total_perolehan: getCleanInteger('#cicilan-total-perolehan'),
            total_ar: getCleanInteger('#cicilan-total-ar'),
            total_bunga: getCleanInteger('#cicilan-total-bunga')
        };

        // console.log(dp, params);
        // return false;

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
            // var checked         = row.find('.dp_is_pengurang_pokok').is(':checked') ? 1 : 0;
            var data = {
                id: row.find('.dp-id').val() || '',
                jenis_komponen: component,
                // dp_is_pengurang_pokok : checked,
                deskripsi: '',
                nominal: nominal,
                angsuran_ke: '',
                jatuh_tempo: ''
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
            var dueDate = moment(row.find('.dp-jatuh-tempo').val(), 'DD MMMM YYYY', 'id', true);
            var nominal = pba.to_backend_nominal(row.find('.dp-nominal').val());
            if (Number(nominal) <= 0 || !row.find('.dp-angsuran-ke').val() || !dueDate.isValid()) {
                valid = false;
            }
            rows.push({
                id: row.find('.dp-id').val() || '',
                // dp_is_pengurang_pokok: '',
                jenis_komponen: 'ANGSURAN',
                deskripsi: '',
                nominal: nominal,
                angsuran_ke: row.find('.dp-angsuran-ke').val() || '',
                jatuh_tempo: dueDate.isValid() ? dueDate.format('YYYY-MM-DD') : ''
            });
        });
        if (!rows.length || !valid) {
            bootbox.alert('Lengkapi nominal setiap baris DP dan nomor serta jatuh tempo untuk komponen angsuran.');
            return;
        }

        var params = {
            kode_pembiayaan: $('#kode_pembiayaan_dp').val(),
            tanggal_dp: date && date.isValid() ? date.format('YYYY-MM-DD') : '',
            rows: rows
        };
        $('#dp-save').prop('disabled', true);
        pba.tdp_request('aset/PembiayaanAset/save_dp', params, $('#dp-save'));
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
        var dateValue = $('#tgl_tdp').val();
        var date = moment(dateValue, 'DD MMMM YYYY', 'id', true);
        if (!date.isValid()) {
            bootbox.alert('Tanggal TDP wajib dipilih.');
            return;
        }

        var rows = [];
        var valid = true;
        $('#tdp-rows .tdp-row').each(function () {
            var description = $.trim($(this).find('.tdp-deskripsi').val() || '');
            var nominal = pba.to_backend_nominal($(this).find('.tdp-nominal').val());
            if (Number(nominal) <= 0) {
                valid = false;
            }
            rows.push({ deskripsi: description, nominal: nominal });
        });
        if (!valid) {
            bootbox.alert('Nominal setiap baris TDP harus lebih dari 0.');
            return;
        }

        var params = {
            kode_pembiayaan: $('#kode_pembiayaan_tdp').val(),
            tanggal: date.format('YYYY-MM-DD'),
            is_pengurang_pokok: $('#tdp_pengurang_pokok').is(':checked') ? 1 : 0,
            rows: rows
        };
        $('#tdp-save').prop('disabled', true);
        pba.tdp_request('aset/PembiayaanAset/save_tdp', params, $('#tdp-save'));
    },

    edit_tdp: function (element) {
        var button = $(element);
        var date = moment(button.data('tanggal'), 'YYYY-MM-DD', true);
        // console.log(date);

        // if (!date.isValid()) {
        //     bootbox.alert('Tanggal TDP tidak valid.');
        //     return;
        // }

        
        var description = $('<div>').text(button.attr('data-deskripsi') || '').html();
        var nominal = pba.format_rupiah(button.attr('data-nominal'));
        var dialog = bootbox.dialog({
            title: 'Ubah TDP',
            message: '<form id="form-edit-tdp">' +
                '<div class="form-group"><label for="edit-tgl-tdp">Tanggal TDP</label>' +
                '<div class="input-group date" id="edit-tgl-tdp-picker"><input type="text" id="edit-tgl-tdp" class="form-control" autocomplete="off" readonly value="' + date.format('DD MMMM YYYY') + '">' +
                '<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>' +
                '<div class="form-group"><label for="edit-deskripsi-tdp">Deskripsi</label><input type="text" id="edit-deskripsi-tdp" class="form-control" maxlength="255" value="' + description + '"></div>' +
                '<div class="form-group"><label for="edit-nominal-tdp">Nominal</label><div class="input-group"><span class="input-group-addon">Rp</span>' +
                '<input type="text" id="edit-nominal-tdp" class="form-control text-right" inputmode="numeric" autocomplete="off" value="' + nominal + '"></div></div>' +
                '<div class="checkbox"><label><input type="checkbox" id="edit-pengurang-pokok-tdp" ' + (String(button.data('pengurang-pokok')) === '1' ? 'checked' : '') + '> Sebagai pengurang pokok hutang</label></div>' +
                '</form>',
            buttons: {
                cancel: {
                    label: 'Batal',
                    className: 'btn-default'
                },
                save: {
                    label: 'Simpan Perubahan',
                    className: 'btn-primary',
                    callback: function () {
                        var editDate = moment($('#edit-tgl-tdp').val(), 'DD MMMM YYYY', 'id', true);
                        var editNominal = pba.to_backend_nominal($('#edit-nominal-tdp').val());
                        if (!editDate.isValid()) {
                            bootbox.alert('Tanggal TDP wajib dipilih.');
                            return false;
                        }
                        if (Number(editNominal) <= 0) {
                            bootbox.alert('Nominal TDP harus lebih dari 0.');
                            return false;
                        }

                        pba.tdp_request('aset/PembiayaanAset/edit_tdp', {
                            id: button.data('id'),
                            tanggal: editDate.format('YYYY-MM-DD'),
                            deskripsi: $('#edit-deskripsi-tdp').val() || '',
                            nominal: editNominal,
                            is_pengurang_pokok: $('#edit-pengurang-pokok-tdp').is(':checked') ? 1 : 0
                        }, button);
                        return true;
                    }
                }
            }
        });

        dialog.on('shown.bs.modal', function () {
            if ($.fn.datetimepicker) {
                $('#edit-tgl-tdp-picker').datetimepicker({
                    locale: 'id',
                    format: 'DD MMMM YYYY',
                    useCurrent: false
                });
            }
        });
        dialog.off('input.pbaEditTdp').on('input.pbaEditTdp', '#edit-nominal-tdp', function () {
            $(this).val(pba.format_rupiah($(this).val()));
        });
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

    process_form: function (element) {
        var button = $(element);
        var id = encodeURIComponent(button.data('id'));
        var kodeJenis = String(button.data('kode-jenis-pembiayaan') || '').toUpperCase();
        
        var urlMap = {
            'PMB01': 'aset/PembiayaanAset/ProsesCash',
            'PMB02': 'aset/PembiayaanAset/ProsesTempo',
            'PMB03': 'aset/PembiayaanAset/ProsesLeasing'
        };
        
        var url = urlMap[kodeJenis] || ('aset/PembiayaanAset/ProsesLeasing?id=' + id);
        
        if (url.indexOf('?') === -1) {
            url += '?id=' + id;
        }
        
        window.open(url, '_blank');
    },

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



    edit_leasing: function (element) {
        var kodePembiayaan = $('#kode_pembiayaan_tdp').val() || $('#kode_pembiayaan_dp').val();

        $.ajax({
            url: 'aset/PembiayaanAset/get_detail_cicilan',
            type: 'POST',
            dataType: 'html',
            data: { 
                params: { 
                    kode_pembiayaan: kodePembiayaan 
                } 
            },
            beforeSend: function () { 
                showLoading(); 
            },
            success: function (response) {  
                bootbox.dialog({
                    title: 'Edit Detail Cicilan',
                    message: response,
                    size: 'large',
                    closeButton: true
                });
            },
            complete: function() {
                hideLoading();
            }
        });
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

        var priceCents = cents(panel.attr('data-harga-beli'));
        var tdpCents = cents(panel.attr('data-tdp-pengurang'));
        var downPaymentCents = cents(panel.attr('data-dp-uang-muka'));
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
        var val = $(element).val().replace(/\D/g, '');
        $(element).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    },


    add_tdp: function (element, event) {
        if (event) event.preventDefault();

        var kodePembiayaan = $('#kode_pembiayaan_tdp').val();
        if (!kodePembiayaan) {
            bootbox.alert('Kode pembiayaan tidak ditemukan.');
            return;
        }

        var dialog;

        $.ajax({
            url: 'aset/PembiayaanAset/get_modal_add_tdp',
            type: 'POST',
            dataType: 'html',
            success: function (html) {

                dialog = bootbox.dialog({
                    title: 'Tambah Tanda Jadi (TDP)',
                    message: html,
                    size: 'large',
                    buttons: {
                        cancel: { 
                            label: 'Batal', 
                            className: 'btn-default' 
                        },
                        success: {
                            label: 'Simpan TDP',
                            className: 'btn-primary',
                            callback: function () {
                                pba.save_tdp_modal(kodePembiayaan);
                                return false; 
                            }
                        }
                    },
        
                    onHide: function () {
                        if ($('.modal-tgl-picker').data('DateTimePicker')) {
                            $('.modal-tgl-picker').data('DateTimePicker').destroy();
                        }
                        $(document).off('keyup.tdpRupiah');
                        $(document).off('change.tdpCheckbox');
                        $(document).off('click.tdpAddRow');
                        $(document).off('click.tdpRemoveRow');
                    }
                });
                
                setTimeout(function() {
                    pba.init_datepickers();
                }, 100);

                $(document).off('keyup.tdpRupiah').on('keyup.tdpRupiah', '.modal-tdp-nominal', function() {
                    var val = $(this).val().replace(/\D/g, '');
                    $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                    pba.hitung_modal_tdp_total();
                });

                $(document).off('change.tdpCheckbox').on('change.tdpCheckbox', '#modal_tdp_pengurang_pokok', function() {
                    pba.hitung_modal_tdp_total();
                });

                $(document).off('click.tdpAddRow').on('click.tdpAddRow', '#modal-add-tdp-row', function() {
                    var newRow = `
                        <div class="row modal-tdp-row" style="margin-bottom:8px;">
                            <div class="col-xs-7">
                                <input type="text" class="form-control modal-tdp-deskripsi" value="Tanda jadi" maxlength="255" placeholder="Deskripsi">
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" class="form-control text-right modal-tdp-nominal" placeholder="0" inputmode="numeric">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <button type="button" class="btn btn-danger btn-sm modal-tdp-remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    `;
                    $('#modal-tdp-rows').append(newRow);
                });

                $(document).off('click.tdpRemoveRow').on('click.tdpRemoveRow', '.modal-tdp-remove', function() {
                    $(this).closest('.modal-tdp-row').remove();
                    pba.hitung_modal_tdp_total();
                });

            },
            error: function () {
                
                bootbox.alert('Gagal memuat form TDP. Silakan coba lagi.');
            }
        });
    },


        // 1. Fungsi untuk menghitung total TDP
    hitung_modal_tdp_total: function() {
        var total = 0;
        
        // Loop semua baris yang ada di modal
        $('.modal-tdp-row').each(function() {
            // Ambil nilai dari input nominal, hapus semua karakter kecuali angka
            var val = $(this).find('.modal-tdp-nominal').val().replace(/\D/g, '');
            // Tambahkan ke total (jika kosong atau bukan angka, anggap 0)
            total += parseFloat(val) || 0;
        });

        // Tampilkan total dengan format Rupiah (contoh: 1.000.000)
        $('#modal-tdp-total').text(total.toLocaleString('id-ID'));
    },

    // 2. Fungsi untuk mengatur tombol hapus (disable jika hanya 1 baris)
    updateModalTdpButtons: function() {
        var count = $('.modal-tdp-row').length;
        
        // Jika baris hanya 1, matikan semua tombol hapus
        if (count <= 1) {
            $('.modal-tdp-remove').prop('disabled', true);
        } 
        // Jika lebih dari 1, aktifkan semua tombol hapus
        else {
            $('.modal-tdp-remove').prop('disabled', false);
        }
    },

        // Pastikan ada tanda koma (,) di akhir fungsi sebelumnya!
    updateModalTdpButtons: function() {
        var count = $('.modal-tdp-row').length;
        if (count <= 1) {
            $('.modal-tdp-remove').prop('disabled', true);
        } else {
            $('.modal-tdp-remove').prop('disabled', false);
        }
    },

    save_tdp_modal: function (kodePembiayaan) {
        // var tanggalStr          = $('#tgl_modal_tdp').val(); 

        var date = moment($('#tgl_modal_tdp').val(), 'DD MMMM YYYY', 'id', true);
        if (!date.isValid()) {
            bootbox.alert('Tanggal pelunasan wajib dipilih.');
            return;
        } 
        
        var tanggalStr = date.format('YYYY-MM-DD')

        // console.log(tanggalStr);
        // return false;
        
        var isPengurangPokok    = $('#modal_tdp_pengurang_pokok').is(':checked') ? 1 : 0;
        
        var rows = [];
        var valid = true;

        // Validasi Tanggal
        // if (!tanggalStr) {
        //     bootbox.alert('Tanggal TDP wajib diisi.');
        //     return;
        // }

        // 2. Loop semua baris untuk ambil data
        $('.modal-tdp-row').each(function(index) {
            var deskripsi = $(this).find('.modal-tdp-deskripsi').val().trim();
            var nominalStr = $(this).find('.modal-tdp-nominal').val().replace(/\./g, ''); 
            
            if (deskripsi === '') {
                bootbox.alert('Deskripsi pada baris ke-' + (index + 1) + ' wajib diisi.');
                valid = false;
                return false; // break loop
            }
            if (!nominalStr || parseFloat(nominalStr) <= 0) {
                bootbox.alert('Nominal pada baris ke-' + (index + 1) + ' harus lebih dari 0.');
                valid = false;
                return false; // break loop
            }

            rows.push({
                tanggal: tanggalStr, // Kirim YYYY-MM-DD
                deskripsi: deskripsi,
                nominal: nominalStr,
                is_pengurang_pokok: isPengurangPokok
            });
        });

        if (!valid) return; // Stop jika ada yang error

        // 3. Kirim ke Server
        $.ajax({
            url: 'aset/PembiayaanAset/save_tdp_modal', // Sesuaikan dengan URL controller Anda
            type: 'POST',
            dataType: 'JSON',
            data: { 
                params: { 
                    kode_pembiayaan: kodePembiayaan,
                    rows: rows
                } 
            },
            beforeSend: function() { 
                if(typeof showLoading === 'function') showLoading(); 
            },
            success: function(response) {
                if(typeof hideLoading === 'function') hideLoading();
                
                if (response.status == 1) {
                    $('.bootbox').modal('hide'); // Tutup modal
                    bootbox.alert({
                        title: 'Berhasil',
                        message: response.message || 'TDP berhasil disimpan.',
                        callback: function() { 
                            window.location.reload(); // Reload halaman
                        }
                    });
                } else {
                    bootbox.alert(response.message || 'Gagal menyimpan TDP.');
                }
            },
            error: function() {
                if(typeof hideLoading === 'function') hideLoading();
                bootbox.alert('Gagal menghubungi server.');
            }
        });
    }
    // --- AKHIR FUNGSI ---


    
};

window.pba = pba;

$(document).ready(function () {
    pba.start_up();
});
