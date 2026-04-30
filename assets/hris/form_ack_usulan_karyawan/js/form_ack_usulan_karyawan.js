let fr = {

    load_form : () => {
        $.ajax({
            url : 'hris/FormAckUsulanKaryawan/load_form',
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

    // show_detail: (elm, e) => {

    //     if (!elm) return;

    //     let id = $(elm).attr("id");

    //     if (!id) return;

    //     let params = { id };

    //     $.ajax({
    //         url: 'hris/FormAckUsulanKaryawan/show_detail',
    //         data: params,
    //         type: 'POST',
    //         dataType: 'html',
    //         beforeSend: function () {
    //             showLoading();
    //         },
    //         success: function (html) {
    //             hideLoading();

    //             bootbox.dialog({
    //                 title: 'Detail Candidate',
    //                 message: html,
    //                 size: 'large',
    //                 buttons: {
    //                     cancel: {
    //                         label: 'Tutup',
    //                         className: 'btn-secondary'
    //                     },
    //                     ack: {
    //                         label: 'Acknowledge',
    //                         className: 'btn-sucees',
    //                         callback: function () {
    //                             fr.approve(params);
    //                         }
    //                     },
    //                     reject: {
    //                         label: 'Reject',
    //                         className: 'btn-danger',
    //                         callback: function () {
    //                             bootbox.confirm('Yakin mau reject?', function (result) {
    //                                 if (result) {
    //                                     fr.delete(params);
    //                                 }
    //                             });
    //                         }
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // },

    // show_biodata: (elm, e) => {
    //     $(elm).closest("fieldset").find(".biodata").slideToggle();
    // },


    filter: (elm, e, val) => {

        let params ={
            status : val,
        }

         $.ajax({
                url : 'hris/FormAckUsulanKaryawan/filter',
                data : params,
                type : 'POST',
                dataType : 'html',
                beforeSend : function(){ 
                    showLoading(); 
                },
                success : function(html){
                    hideLoading();

                    $(".list_data").html(html)
                
                },
            });

    },

    show_usulan: (elm, e) => {

        let params = {
            nama_pengusul: $(elm).closest("tr").attr("nama_pengusul"),
            tgl_pengusul: $(elm).closest("tr").attr("tgl_pengusul"),
            posisi: $(elm).closest("tr").attr("posisi"),
            alasan: $(elm).closest("tr").attr("alasan"),
            jumlah: $(elm).closest("tr").attr("jumlah"),
            unit: $(elm).closest("tr").attr("unit"),
            status: $(elm).closest("tr").attr("status"),
            id_data: $(elm).closest("tr").attr("id_data"),
            document: $(elm).closest("tr").attr("document"),
            status_key : $(elm).closest("tr").attr("status_key"),
            encrypted : $(elm).closest("tr").attr("encrypted"),
            keterangan_ceo : $(elm).closest("tr").attr("keterangan_ceo"),
            keterangan_hrd : $(elm).closest("tr").attr("keterangan_hrd"),
        }

        // console.log(params);
        // return false;
        let html = `
            <style>
                .detail-container {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 12px;
                    font-family: Arial, sans-serif;
                }
     
                .field {
                    display: flex;
                    flex-direction: column;
                }

                .field.full {
                    grid-column: span 2;
                }

                .field label {
                    font-size: 12px;
                    color: #777;
                    margin-bottom: 4px;
                }

                .value {
                    background: #f5f5f5;
                    padding: 10px;
                    border-radius: 6px;
                    font-weight: 500;
                }

                .status-1 { background: #F7F599; }
                .status-2 { background: #AAF799; }
                .status-3 { background: #2283D6; color: white; }
                .status-4 { background: #F76363; color: white; }

                @media (max-width: 576px) {
                    .detail-container {
                        grid-template-columns: 1fr;
                    }

                    .field.full {
                        grid-column: span 1;
                    }
                }
                </style>

            <div class="detail-container">

                <div class="field full">
                    <label>Document</label>
                    <div class="value">${params.document || '-'}</div>
                </div>

                <div class="field">
                    <label>Nama Pengusul</label>
                    <div class="value">${params.nama_pengusul || '-'}</div>
                </div>

                <div class="field">
                    <label>Tanggal Pengusulan</label>
                    <div class="value">${params.tgl_pengusul || '-'}</div>
                </div>

                <div class="field">
                    <label>Posisi</label>
                    <div class="value">${params.posisi || '-'}</div>
                </div>

                <div class="field">
                    <label>Jumlah</label>
                    <div class="value">${params.jumlah || '-'}</div>
                </div>

                <div class="field">
                    <label>Unit</label>
                    <div class="value">${params.unit || '-'}</div>
                </div>

                <div class="field">
                    <label>Status</label>
                    <div class="value status status-${params.status}">${params.status || '-'}</div>
                </div>

                <div class="field full">
                    <label>Alasan</label>
                    <div class="value">${params.alasan || '-'}</div>
                </div>  `;

                if(params.status_key == 4 ){
                    html += `<div class="field full">
                        <label>Keterangan Reject</label>
                        <div class="value">${params.keterangan_hrd || '-'}</div>
                    </div> `;
                } 

                // console.log(params)
                if(params.status_key == 5 ){
                    html += `<div class="field full">
                        <label>Keterangan Reject</label>
                        <div class="value">${params.keterangan_ceo || '-'}</div>
                    </div> `;
                } 

            html +=`</div>`;

            let buttons = {
                cancel: {
                    label: '<i class="fa fa-times" aria-hidden="true"></i> Tutup',
                    className: 'btn-secondary'
                }
            };

            if (params.status_key == 1) {
                buttons.ack = {
                    label: 'Acknowledge',
                    className: 'btn-success',
                    callback: function () {
                        fr.keputusan(params.id_data, 2);
                    }
                };

                buttons.reject = {
                    label: 'Reject',
                    className: 'btn-danger',
                    callback: function () {
                        bootbox.confirm('Yakin mau reject?', function (result) {
                            if (result) {
                                fr.keputusan(params.id_data, 4);
                            }
                        });
                    }
                };
            }

            else if (params.status_key == 2) {
                buttons.print = {
                    label : '<i class="fa fa-print" aria-hidden="true"></i> Print',
                    className: 'btn btn-info',
                        callback: function () {
                        fr.print_data(params.encrypted);
                    }
                    
                }

                buttons.approve = {
                    label: 'Approve',
                    className: 'btn-primary',
                    callback: function () {
                        fr.keputusan(params.id_data, 3);
                    }
                };

                buttons.reject = {
                    label: 'Reject',
                    className: 'btn-danger',
                    callback: function () {
                        
                                fr.keputusan(params.id_data, 5);
                            
                    
                    }
                };
            } 

            else if (params.status_key == 3) {
                buttons.print = {
                    label : '<i class="fa fa-print" aria-hidden="true"></i> Print',
                    className: 'btn btn-info',
                        callback: function () {
                        fr.print_data(params.encrypted);
                    }
                };

                buttons.done = {
                    label: '<i class="fa fa-check" aria-hidden="true"></i> Done',
                    className: 'btn-success',
                    callback: function () {
                        fr.keputusan(params.id_data, 6);
                    }
                };
            } else if (params.status_key = 6) {
                buttons.print = {
                    label : '<i class="fa fa-print" aria-hidden="true"></i> Print',
                    className: 'btn btn-info',
                        callback: function () {
                        fr.print_data(params.encrypted);
                    }
                };
            }
        bootbox.dialog({
            title: 'Detail Usulan',
            message: html,
            size: 'large',
            buttons: buttons,
        });

    },


   keputusan: (id_data, val) => {
    
        const STATUS = {
            DRAFT: 1,
            ACK: 2,
            APPROVE: 3,
            REJECTHRD: 4,
            REJECTCEO: 5,
            DONE: 6,
        };

        let text = val == STATUS.ACK ? 'Acknowledge' 
                : val == STATUS.APPROVE ? 'Approve' 
                : val == STATUS.REJECTHRD ? 'Reject' 
                : val == STATUS.REJECTCEO ? 'Reject' 
                : val == STATUS.DONE ? 'Done' 
                : 'DRAFT';


        if (val == STATUS.REJECTHRD || val == STATUS.REJECTCEO) {
            bootbox.prompt({
                title: "Masukkan alasan reject",
                inputType: 'textarea',
                callback: function(result) {
                    if (result === null) return; 

                    if (!result.trim()) {
                        bootbox.alert('Keterangan wajib diisi!');
                        return;
                    }

                    fr.exec_keputusan(id_data, val, result);
                }
            });

        } else {
            bootbox.confirm(`Apakah Anda yakin ingin ${text} pengusulan ini?`, function(result){
                if(!result) return;

                fr.exec_keputusan(id_data, val, null);
            });
        }
    },

    exec_keputusan:(id_data, val, keterangan = null) => {

        let params = {
            keputusan : val,
            id_data : id_data,
            keterangan : keterangan
        };

        $.ajax({
            url : 'hris/FormAckUsulanKaryawan/update',
            data : params,
            type : 'POST',
            dataType : 'json',
            beforeSend : function(){ 
                showLoading(); 
            },
            success : function(data){
                hideLoading();

                bootbox.alert(data.message, function () {
                    fr.load_form();
                });
            },
        });
    },

    print_data: (data) => {
        // console.log(data);
        window.open(`hris/FormAckUsulanKaryawan/printPreview?id=${data}`, '_blank');
    },
}

$(document).ready(function() {
    fr.load_form();
});
