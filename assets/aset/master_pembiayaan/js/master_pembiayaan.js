let mp = {
    start_up: function () {
        mp.bind_tab_events();
        mp.load_data();
    },

    bind_tab_events: function () {
        $('a[data-toggle="tab"]').off('click.mpTab').on('click.mpTab', function (event) {
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
                mp.add_form();
            }
        });
    },

    open_action_tab: function (html) {
        $('#tab-action-content').html(html);
        $('a[href="#action"]').trigger('click');
    },

    add_form: function () {
        $.get('aset/MasterPembiayaan/add_form', function (html) {
            mp.open_action_tab(html);
        }, 'html').fail(function () {
            bootbox.alert('Form pembiayaan gagal dimuat.');
        });
    },

    edit_form: function (element) {
        $.get('aset/MasterPembiayaan/edit_form', { id: $(element).data('id') }, function (html) {
            mp.open_action_tab(html);
        }, 'html').fail(function () {
            bootbox.alert('Data pembiayaan gagal dimuat.');
        });
    },

    load_data: function () {
        $.ajax({
            url: 'aset/MasterPembiayaan/list_data',
            type: 'GET',
            dataType: 'HTML',
            beforeSend: function () { showLoading(); },
            success: function (html) {
                $('.table-area').html(html);
                hideLoading();
            },
            error: function () {
                hideLoading();
                bootbox.alert('Daftar pembiayaan gagal dimuat.');
            }
        });
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
                    bootbox.alert('Permintaan pembiayaan gagal diproses.');
                }
            });
        });
    },

    get_form_params: function () {
        var name = $.trim($('#nama_pembiayaan').val() || '');
        if (name === '') {
            bootbox.alert('Jenis pembiayaan wajib diisi.');
            return null;
        }
        return {
            nama_pembiayaan: name
        };
    },

    save_data: function () {
        var params = mp.get_form_params();
        if (!params) {
            return;
        }
        mp.submit('aset/MasterPembiayaan/save_data', params, 'Simpan jenis pembiayaan ini?');
    },

    edit_data: function () {
        var params = mp.get_form_params();
        if (!params) {
            return;
        }
        params.id = $('#id_pembiayaan').val();
        mp.submit('aset/MasterPembiayaan/edit_data', params, 'Simpan perubahan jenis pembiayaan?');
    },

    delete_data: function (element) {
        mp.submit('aset/MasterPembiayaan/delete_data', $(element).data('id'), 'Hapus jenis pembiayaan ini?');
    }
};

window.mp = mp;

$(document).ready(function () {
    mp.start_up();
});
