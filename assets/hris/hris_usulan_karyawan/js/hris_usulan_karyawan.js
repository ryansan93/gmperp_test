
let masterOptions = '';

let hf ={

    start_up : () =>{

        $('#tgl_pengusulan').datetimepicker({
            locale: 'id',
                format: 'DD MMM YYYY'
        });

    },

    add_row: (elm, e) => {

        let parent = $(elm).closest('.detail_form');

        let options = parent.find('.nama_kandidat').html();

        let html = `
        <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                <label style="width:10%;">Nama</label>
                
                <select class="select2 nama_kandidat" style="width:40%;">
                    ${options}
                </select>

                <div style="width:40%; text-align:right">
                    <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                    <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                </div>
            </div>
        </div>`;

        $(".detail_area").append(html);

        $(".detail_area .detail_form:last .nama_kandidat").find('option:first').prop('selected', true).trigger('change');

        $('.select2').select2();

        let jumlah_dtl = $(".detail_form").length;
        $(".jumlah").val(jumlah_dtl);

        
    },
    
    delete_row : (elm, e) => {

        let dtl = $(".detail_form").length;

        if(dtl <= 1){
            bootbox.alert('Baris tidak boleh lebih dari 1');
        } else {
            $(elm).closest(".detail_form").remove();
        }

        // console.log(dtl)

        $(".jumlah").val(dtl -1);
    },

    save: (elm, e)  => {

        let header = {
            mengusulkan     : $(".mengusulkan").val(),
            tgl_pengusulan  : dateSQL($('#tgl_pengusulan').data('DateTimePicker').date()),
            posisi          : $(".posisi").val(),
            jumlah          : $(".jumlah").val(),
            unit            : $(".unit").val(),
            alasan          : $(".alasan").val(),
        }

        if (header.mengusulkan === "") {
            return bootbox.alert("Pengusul wajib diisi!");
        }

        if (header.tgl_pengusulan === "") {
            return bootbox.alert("Tanggal wajib diisi!");
        }

        if (header.posisi === "") {
            return bootbox.alert("Posisi wajib diisi!");
        }

        if (header.jumlah === "" || isNaN(header.jumlah)) {
            return bootbox.alert("Urutan harus berupa angka!");
        }

        if (header.unit === "") {
            return bootbox.alert("Unit wajib dipilih!");
        }

         if (header.alasan === "") {
            return bootbox.alert("Alasan wajib dipilih!");
        }

        let detail = [];
        let isValidDetail = true;

        $(".detail_area").find(".detail_form").each(function(index){
            let nama_kandidat = $(this).find(".nama_kandidat").val().trim();

            if (nama_kandidat === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${index + 1} tidak boleh kosong!`);
                return false;
            }

            detail.push({
                id_kandidat: nama_kandidat,
            });
        });

        if (!isValidDetail) return;

        if (detail.length === 0) {
            return bootbox.alert("Minimal harus ada 1 detail!");
        }

        let params = {
            header : header,
            detail : detail,
        }


        $.ajax({
            url : 'hris/HrisUsulanKaryawan/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisUsulanKaryawan';
                });
            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/HrisUsulanKaryawan/load_form',
            // data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                // showLoading(); 
            },
            success : function(html){
                hideLoading();

                $(".list_data").html(html)
                hf.start_up();
               
            },
        });
    },

    filter_data : () => {
        let params ={
            pengaju : $(".pengaju-filter").val(),
        }

        $.ajax({
            url : 'hris/HrisUsulanKaryawan/filter_data',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                // showLoading(); 
            },
            success : function(html){
                hideLoading();

                $(".list_data").html(html)
               
            },
        });
    },

    
    edit: (params) => {
        let url = 'hris/HrisUsulanKaryawan/edit_data?id_data=' + params.id ;
        window.location.href = url ;
    },

    changeTabActive: () => {
        $('a[href="#action"]').tab('show');
    },


    update: (elm, e)  => {

        let header = {

            mengusulkan     : $(".mengusulkan").val(),
            tgl_pengusulan  : dateSQL($('#tgl_pengusulan').data('DateTimePicker').date()),
            posisi          : $(".posisi").val(),
            jumlah          : $(".jumlah").val(),
            unit            : $(".unit").val(),
            alasan          : $(".alasan").val(),

        }

        let detail = [];

        $(".detail_area").find(".detail_form").each(function(){
            let nama_kandidat = $(this).find(".nama_kandidat").val();

            if (nama_kandidat !== "") {
                let detail_temp = {
                    id_kandidat : nama_kandidat,
                };

                detail.push(detail_temp);
            }
        });

        let params = {
            id_data : $(elm).attr("id_data"),
            header : header,
            detail : detail,
        }

        // console.log(params);
        // return false;

        $.ajax({
            url : 'hris/HrisUsulanKaryawan/update',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisUsulanKaryawan';
                });
               
            },
        });
    },


    delete: (elm, e) =>{

        let id_data = $(elm).attr("id_data");

        if (!id_data) {
            return bootbox.alert("Data tidak valid!");
        }

        bootbox.confirm({
            message: "Apakah Anda yakin ingin menghapus data ini?",
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Batal',
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {

                if (!result) return;

                let params = {
                    id_data : id_data
                };

                $.ajax({
                    url : 'hris/HrisUsulanKaryawan/delete',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){ 
                        showLoading(); 
                    },
                    success : function(data){
                        hideLoading();

                        bootbox.alert(data.message, function () {
                            window.location.href = 'hris/HrisUsulanKaryawan';
                        });
                    },
                    error: function(){
                        hideLoading();
                        bootbox.alert("Terjadi kesalahan saat menghapus data!");
                    }
                });

            }
        });

    },

    show_kategori_list: function() {

        $(".kategori_list").show();

    },

    select_kategori_list: (elm, e) =>{

        let kode_kategori = $(elm).attr("value_kategori");
        let nama_kategori = $(elm).html();

        $(".kategori").val(nama_kategori);
        $(".kategori").attr('kode_kategori', kode_kategori);


        $(".kategori_list").hide();
    }
}

let app = {

    init: function() {

        $(".kategori").on("click", function() {
            $(".kategori_list").show();
        });

        $(".kategori_list div").on("click", function() {
            let nama = $(this).text();
            let kode = $(this).attr("value_kategori");

            $(".kategori").val(nama);
            $(".kategori").attr("kode_kategori", kode);
            $(".kategori_list").hide();
        });

        $(document).on("click", function(e) {
            if (!$(e.target).closest(".kategori, .kategori_list").length) {
                $(".kategori_list").hide();
            }
        });

    },

    filter_kandidat: () => {

    let selectedValues = [];

    $('.nama_kandidat').each(function(){
        let val = $(this).val();
        if (val) selectedValues.push(val);
    });

    $('.nama_kandidat').each(function(){

        let current = $(this).val();

        // reset ke master option
        $(this).html(masterOptions);

        // hapus yang sudah dipilih di select lain
        $(this).find('option').each(function(){
            let val = $(this).val();

            if (selectedValues.includes(val) && val !== current) {
                $(this).remove();
            }
        });

        // set kembali value sebelumnya
        $(this).val(current);

    });

    // re-init select2
    $('.nama_kandidat').each(function(){
        $(this).select2('destroy').select2();
    });
}


};


$(document).ready(function() {
    app.init();
    hf.load_form();
    hf.start_up()

    $('.select2').select2();
});

