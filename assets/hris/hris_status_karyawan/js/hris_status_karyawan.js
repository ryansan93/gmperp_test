// const { data } = require("autoprefixer");

let hf ={

    add_row: (elm, e) => {

        let selectKategori = $(elm).closest('.detail_form').find('.kategori').html(); 

        let html = `
            <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                <div style="display:flex; flex-direction:row; gap:20px; align-items:center;" >
                    <label style="width:20%;">Nama Status</label>
                    <input type="text" class="form form-control nama_status" style="width:60%;">

                    <label style="width:20%;">Pilih Kategori</label>
                    <select class="form form-control kategori" style="width:20%;">
                        ${selectKategori}
                    </select>
                    
                    <div style="width:10%; text-align:right">
                        <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                        <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                    </div>
                </div>

            </div>`;

        $(".detail_area").append(html);
    },

    delete_row : (elm, e) => {

        let dtl = $(".detail_form").length;

        if(dtl <= 1){
            bootbox.alert('Baris tidak boleh lebih dari 1');
        } else {
            $(elm).closest(".detail_form").remove();
        }
    },

    save: (elm, e)  => {

        let detail = [];
        let isValidDetail = true;

        $(".detail_area").find(".detail_form").each(function(index){
            let nama_status = $(this).find(".nama_status").val().trim();
            let kategori = $(this).find(".kategori").val().trim();

            if (nama_status === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${nama_status + 1} tidak boleh kosong!`);
                return false;
            }

             if (kategori === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${kategori + 1} tidak boleh kosong!`);
                return false;
            }

            detail.push({
                nama_status: nama_status,
                kategori: kategori,
            });
        });

        if (!isValidDetail) return;

        if (detail.length === 0) {
            return bootbox.alert("Minimal harus ada 1 detail!");
        }

        let params = {
            data : detail,
        }

        $.ajax({
            url : 'hris/HrisStatusKaryawan/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisStatusKaryawan';
                });
            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/HrisStatusKaryawan/load_form',
            // data : params,
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

    filter_data : () => {
        let params ={
            kategori : $("#kategori").val(),
        }

        $.ajax({
            url : 'hris/HrisStatusKaryawan/filter_data',
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


    edit: (elm, e) => {

        let params = {
            id_data : $(elm).attr("id_data"),
        }
        
        $.ajax({
            url : 'hris/HrisStatusKaryawan/edit_data',
            data : params,
            type : 'POST',
            dataType : 'html',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(html){
                hideLoading();

                bootbox.dialog({
                    title: 'Detail Data', 
                    message: html,
                    size: 'large',
                    buttons: {
                        cancel: {
                            label: 'Tutup',
                            className: 'btn-secondary'
                        },
                        edit: {
                            label: 'Update',
                            className: 'btn-primary',
                            callback: function () {

                                let modal = $(".bootbox");

                                let data_params = {
                                    id_data : modal.find(".id_data").attr("kode"),
                                    nama_status : modal.find(".nama_status").val(),
                                    kategori : modal.find(".kategori").val(),
                                }

                                // console.log(data_params)
                                hf.update(data_params);
                            }
                        },
                    }
                });
               
            },
        });
    },

    changeTabActive: () => {
        $('a[href="#action"]').tab('show');
    },


    update: (data_params)  => {

        $.ajax({
            url : 'hris/HrisStatusKaryawan/update',
            data : data_params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    // window.location.href = 'hris/HrisStatusKaryawan';

                    hf.load_form();
                });
               
            },
        });
    },


    delete: (elm, e) =>{

        let params = {
            id_data : $(elm).attr('id_data'),
        }

        bootbox.confirm('Yakin mau hapus?', function(result) {
            if (result) {
                $.ajax({
                    url : 'hris/HrisStatusKaryawan/delete',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){ 
                        showLoading(); 
                    },
                    success : function(data){
                        hideLoading();

                        bootbox.alert(data.message, function () {
                            window.location.href = 'hris/HrisStatusKaryawan';
                        });
                    },
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

    }
};


$(document).ready(function() {
    app.init();
    hf.load_form();
});

