var stok = {
    startUp: function() {
        stok.settingUp();
    }, // end - stok

    settingUp: function() {
        $('select.bulan').select2();
        $('select.barang').select2();
        $('select.gudang').select2().on("select2:select", function (e) {
            stok.getBarang();
        });

        $('#Tahun').datetimepicker({
            locale: 'id',
            format: 'Y'
        });

        stok.getBarang();
    }, // end - stok

    getBarang: function() {
        var jenis_gudang = $('select.gudang').find('option:selected').attr('data-jenis');

        $('select.barang').find('option').removeAttr('disabled');
        $('select.barang').find('option:not([data-jenis="'+jenis_gudang+'"])').attr('disabled', 'disabled');
        $('select.barang').find('option[value="all"]').removeAttr('disabled');

        $('select.barang').select2();
    }, // end - getData

    getData: function() {
        var err = 0;
        $.map( $('[data-required=1]'), function (ipt) {
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
            var dcontent = $('table tbody');
			var params = {
				'bulan': $('.bulan').select2().val(),
				'gudang': $('.gudang').select2().val(),
				'barang': $('.barang').select2().val(),
				'jenis': $('select.gudang').find('option:selected').attr('data-jenis'),
				'tahun': dateSQL( $('#Tahun').data('DateTimePicker').date() )
			};

			$.ajax({
                url : 'report/KartuStok/getData',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ App.showLoaderInContent( $(dcontent) ); },
                success : function(html){
                	App.hideLoaderInContent( $(dcontent), html );
                }
            });
		}
    }, // end - getData
};

stok.startUp();