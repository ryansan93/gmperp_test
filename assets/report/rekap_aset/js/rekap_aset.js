let ra = {
    start_up: function () {
        ra.init_select2();
        ra.load_data(); 
        ra.bind_search_filter();
    },

    init_select2: function () {
        if (!$.fn.select2) return;

        $('#filter_kategori_aset, #filter_status').each(function () {
            var select = $(this);
            var isFilter = select.attr('id').indexOf('filter_') === 0;

            if (select.data('select2')) {
                select.select2('destroy');
            }

            var parentElement = $(document.body); 
            
            if (isFilter) {
                if ($('#history').length > 0) {
                    parentElement = $('#history');
                }
            } else {
                if ($('#tab-action-content').length > 0) {
                    parentElement = $('#tab-action-content');
                }
            }

            select.select2({
                width: '100%',
                allowClear: true, 
                placeholder: isFilter ? "Semua Data" : "Pilih data...", 
                dropdownParent: parentElement,
                theme: 'bootstrap'
            });

            var currentVal = select.val();
            if (currentVal !== undefined && currentVal !== "") {
                select.trigger('change');
            }
        });
    },

    load_data: function () {
        $.ajax({
            url: 'report/RekapAset/list_data',
            type: 'GET',
            dataType: 'HTML',
            beforeSend: function () {
                showLoading();
            },
            success: function (data) {
                $('#table-rekap-aset tbody').html(data);
                hideLoading();
            },
            error: function () {
                hideLoading();
                console.log('Error get data from ajax');
            }
        });
    },

    resetFilter: function (event) {
        if (event) { event.preventDefault(); }
        $('#filter_kategori_aset').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        $('#search_aset').val('');
        ra.filterData(event);
    },

    bind_search_filter: function () {
     
        $(document).off('input.maSearchFilter', '#search_aset').on('input.maSearchFilter', '#search_aset', function () {
            clearTimeout(ra.searchTimer);
            ra.searchTimer = setTimeout(function () {
                ra.filterData();
            }, 300);
        });
    },


    filterData: function (event) {
        if (event) { event.preventDefault(); }

        $.ajax({
            url: 'report/RekapAset/list_data',
            type: 'POST',
            data: {
                id_kategori: $('#filter_kategori_aset').val() || '',
                filter_status: $('#filter_status').val() || '',
                search: $('#search_aset').val() || ''
            },
            dataType: 'HTML',
            success: function (data) {
                $('#table-rekap-aset tbody').html(data);
            },
            error: function () {
                console.log('Error filter data from ajax');
            }
        });
    },

    exportData: function (event) {
        
        let hasData = $("#table-rekap-aset tbody .tr_loop").filter(function() {
            return $(this).find('td').text().trim() !== 'Tidak ada data tersedia';
        }).length > 0;

        if (!hasData) {
            bootbox.alert('Tidak ada data untuk di export');
            return;
        }
        
        if (event) {
            event.preventDefault();
        }

        var filters = {
            id_kategori: $('#filter_kategori_aset').val() || '',
            filter_status: $('#filter_status').val() || '',
            search: $('#search_aset').val() || ''
        };

        var url = 'report/RekapAset/export_data?type=xlsx&' + $.param(filters);
        window.location.href = url;
    },


    show_detail: function (event) {
        
        var kode = $(elm).data('kode');

        $.ajax({
            url: 'report/RekapAset/detail_data',
            type: 'POST',
            dataType: 'HTML',
            data: { params: kode },
            success: function (html) {
                bootbox.alert({
                    title: 'Detail Aset',
                    message: html,
                    size: 'large',
                    buttons: {
                        ok: {
                            label: 'Tutup',
                            className: 'btn-primary'
                        }
                    }
                });
            },
            error: function () {
                bootbox.alert('Terjadi kesalahan saat memuat detail data.');
            }
        });
    },

};

$(document).ready(function () {
    ra.start_up();
});