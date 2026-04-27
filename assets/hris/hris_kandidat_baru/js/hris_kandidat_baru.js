let hf = {
    add_row: (elm, e) => {

        let html = `
            <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                <div style="display:flex; flex-direction:row; gap:20px; align-items:center;" class="detail_form">
                    <label style="width:20%;">Nama karyawan</label>
                    <input type="text" class="form form-control nama_kategori" style="width:60%;">
        

                    <label style="width:20%;">Status karyawan</label>
                    <select class="form form-control status_karyawan" style="width:20%;">
                        <option value="Interview">Interview</option>
                        <option value="Training">Training</option>
                        <option value="Tetap">Tetap</option>
                    </select>
                    
                    <div style="width:10%; text-align:right">
                        <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                        <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                    </div>
                </div>
                
            </div>`;

        $(".detail_area").append(html);
    },

    save: (elm, e)  => {

        let detail = [];
        let isValidDetail = true;

        $(".detail_area").find(".detail_form").each(function(){
            let nama_karyawan = $(this).find(".nama_kategori").val().trim();
            let status_karyawan = $(this).find(".status_karyawan").val();

            if (nama_karyawan === "") {
                isValidDetail = false;
                bootbox.alert(`Label pada detail ke-${nama_karyawan + 1} tidak boleh kosong!`);
                return false;
            }

            detail.push({
                nama_karyawan: nama_karyawan,
                status_karyawan: status_karyawan,
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
            url : 'hris/HrisKandidatBaru/save',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    window.location.href = 'hris/HrisKandidatBaru';
                });
            },
        });
    },

    load_form : () => {
        $.ajax({
            url : 'hris/HrisKandidatBaru/load_form',
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
}

$(document).ready(function() {
    // app.init();
    hf.load_form();
});
