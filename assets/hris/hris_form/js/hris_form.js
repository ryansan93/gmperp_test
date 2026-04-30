let hf ={

    add_row: (elm, e) => {

        let next_urutan = $(".detail_form").length + 1;

        console.log(next_urutan)

        let html = `
        <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">
            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                <label style="width:10%;">Label</label>
                <input type="text" class="form form-control label_dtl" style="width:40%;">
                
                <input type="text" placeholder="urutan" 
                    class="form form-control urutan_dtl" 
                    value="${next_urutan}" 
                    style="width:10%;">

                <input type="text" placeholder="parent label" class="form form-control parent_label" style="width:10%;">
                
                <div style="width:40%; text-align:right">
                    <button class="btn btn-warning" onclick="hf.add_row(this, event);">
                        <span class="fa fa-plus"></span>
                    </button>
                    <button class="btn btn-danger" onclick="hf.delete_row(this, event);">
                        <span class="fa fa-close"></span>
                    </button>   
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

        let header = {
            title : $(".title_hdr").val().trim(),
            keterangan : $(".keterangan").val().trim(),
            urutan : $(".urutan_hdr").val(),
            kategori : $(".kategori").attr("kode_kategori"),
        }

        if (header.title === "") {
            return bootbox.alert("Title wajib diisi!");
        }

        if (header.keterangan === "") {
            return bootbox.alert("Keterangan wajib diisi!");
        }

        if (header.urutan === "" || isNaN(header.urutan)) {
            return bootbox.alert("Urutan harus berupa angka!");
        }

        if (header.kategori === "") {
            return bootbox.alert("Kategori wajib dipilih!");
        }

        let detail = [];
        let isValidDetail = true;

        $(".detail_area").find(".detail_form").each(function(index){
            let label = $(this).find(".label_dtl").val().trim();
            let urutan = $(this).find(".urutan_dtl").val();
            let parent_label = $(this).find(".parent_label").val();

            if (label === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${index + 1} tidak boleh kosong!`);
                return false;
            }

            if (urutan === "" || isNaN(urutan)) {
                isValidDetail = false;
                bootbox.alert(`Urutan pada detail ke-${index + 1} harus angka!`);
                return false;
            }

            detail.push({
                label: label,
                urutan: urutan,
                parent_label : parent_label,
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
            url : 'hris/HrisForm/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisForm';
                });
            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/HrisForm/load_form',
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
        // let params ={
        //     kategori : $("#kategori").val(),
        // }

        // $.ajax({
        //     url : 'hris/HrisForm/filter_data',
        //     data : params,
        //     type : 'POST',
        //     dataType : 'html',
        //     beforeSend : function(){ 
        //         // showLoading(); 
        //     },
        //     success : function(html){
        //         hideLoading();

        //         $(".list_data").html(html)
               
        //     },
        // });

        let value = $(".pengaju-filter").val().toLowerCase();
        let visibleCount = 0;

        $(".table tbody tr.data-row").each(function () {
            let text = $(this).text().toLowerCase();
            let match = text.indexOf(value) > -1;

            $(this).toggle(match);

            if (match) visibleCount++;
        });

        if (visibleCount === 0) {
            $(".no-data").show();
        } else {
            $(".no-data").hide();
        }
        
    },

    show_detail :(elm, e) =>{

        let params ={
            id : $(elm).attr("id"),
        }

        $.ajax({
            url : 'hris/HrisForm/show_detail',
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

    edit: (params) => {
        let url = 'hris/HrisForm/edit_data?id_data=' + params.id ;
        window.location.href = url ;
    },

    changeTabActive: () => {
        $('a[href="#action"]').tab('show');
    },


    update: (elm, e)  => {

        let header = {
            title : $(".title_hdr").val(),
            keterangan : $(".keterangan").val(),
            urutan : $(".urutan_hdr").val(),
            kategori : $(".kategori").attr("kode_kategori"),
        }

        let detail = [];

       $(".detail_area").find(".detail_form").each(function(){
            let label = $(this).find(".label_dtl").val().trim();
            let urutan = $(this).find(".urutan_dtl").val();

            if (label !== "") {
                let detail_temp = {
                    label: label,
                    urutan: urutan,
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
            url : 'hris/HrisForm/update',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisForm';
                });
               
            },
        });
    },


    delete: (params) =>{

         $.ajax({
            url : 'hris/HrisForm/delete',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisForm';
                });
            },
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

