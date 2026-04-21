let hf ={

    add_row : (elm,e) =>{

        let html = ` <div style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                        <div style="display:flex; flex-direction:row; gap:10px; align-items:center;" class="detail_form">
                            <label style="width:10%;">Label</label>
                            <input type="text" class="form form-control label_dtl" style="width:40%;">
                
                            <input type="text" placeholder="urutan" class="form form-control urutan_dtl" style="width:10%;">
                            
                            <div style="width:40%; text-align:right">
                                <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                                <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                            </div>
                        </div>
                    </div> `;

        $(".detail_area").append(html)

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
            title : $(".title_hdr").val(),
            keterangan : $(".keterangan").val(),
            urutan : $(".urutan_hdr").val(),
            kategori : $(".kategori").val(),
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
            header : header,
            detail : detail,
        }

        // console.log(params);
        // return false;

        $.ajax({
            url : 'master/HrisForm/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'master/HrisForm';
                });

            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'master/HrisForm/load_form',
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
            url : 'master/HrisForm/filter_data',
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
            url : 'master/HrisForm/show_detail',
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
        let url = 'master/HrisForm/edit_data?id_data=' + params.id ;
        window.location.href = url ;
    },


    update: (elm, e)  => {

        let header = {
            title : $(".title_hdr").val(),
            keterangan : $(".keterangan").val(),
            urutan : $(".urutan_hdr").val(),
            kategori : $(".kategori").val(),
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
            url : 'master/HrisForm/update',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'master/HrisForm';
                });
               
            },
        });
    },


    delete: (params) =>{

         $.ajax({
            url : 'master/HrisForm/delete',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'master/HrisForm';
                });
            },
        });

    },

    generate_form: (elm, e) => {

    },
}


hf.load_form();