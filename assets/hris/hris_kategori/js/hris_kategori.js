// const { data } = require("autoprefixer");

let hf ={

    add_row: (elm, e) => {

        let html = `
        <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">
            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                <label style="width:10%;">Nama Kategori</label>
                <input type="text" class="form form-control nama_kategori" style="width:40%;">
                
                <div style="width:40%; text-align:right">
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
            let nama_kategori = $(this).find(".nama_kategori").val().trim();

            if (nama_kategori === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${nama_kategori + 1} tidak boleh kosong!`);
                return false;
            }

            detail.push({
                nama_kategori: nama_kategori,
            });
        });

        if (!isValidDetail) return;

        if (detail.length === 0) {
            return bootbox.alert("Minimal harus ada 1 detail!");
        }

        let params = {
            detail : detail,
        }

        $.ajax({
            url : 'hris/HrisKategori/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisKategori';
                });
            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/HrisKategori/load_form',
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
            url : 'hris/HrisKategori/filter_data',
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

    show_detail :(elm, e) =>{

        let params ={
            id : $(elm).attr("id"),
        }

        $.ajax({
            url : 'hris/HrisKategori/show_detail',
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
                            label: 'Edit',
                            className: 'btn-primary',
                            callback: function () {
                                hf.edit(params);
                            }
                        },
                        delete: {
                            label: 'Hapus',
                            className: 'btn-danger',
                            callback: function () {
  
                                bootbox.confirm('Yakin mau hapus?', function(result) {
                                    if (result) {
                                        hf.delete(params);
                                    }
                                });
                            }
                        }
                    }
                });
               
            },
        });
    },

    edit: (elm, e) => {

        let params = {
            kode_kategori : $(elm).attr("kode_kategori"),
        }
        
        $.ajax({
            url : 'hris/HrisKategori/edit_data',
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
                                    kode_kategori : modal.find(".kode_kategori").attr("kode"),
                                    nama_kategori : modal.find(".nama_kategori").val(),
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
            url : 'hris/HrisKategori/update',
            data : data_params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    // window.location.href = 'hris/HrisKategori';

                    hf.load_form();
                });
               
            },
        });
    },


    delete: (elm, e) =>{

        let params = {
            kode_kategori : $(elm).attr('kode_kategori'),
        }

        bootbox.confirm('Yakin mau hapus?', function(result) {
            if (result) {
                $.ajax({
                    url : 'hris/HrisKategori/delete',
                    data : params,
                    type : 'POST',
                    dataType : 'json',
                    beforeSend : function(){ 
                        showLoading(); 
                    },
                    success : function(data){
                        hideLoading();

                        bootbox.alert(data.message, function () {
                            window.location.href = 'hris/HrisKategori';
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

