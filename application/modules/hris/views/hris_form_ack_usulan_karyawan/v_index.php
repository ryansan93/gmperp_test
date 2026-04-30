<style id="k9d3a1">
    .btn-filter {
        border-radius: 5px;
        border: none;
        padding: 10px;
        flex: 1 1 150px; 
        cursor: pointer;
        font-weight: bold;
    }

    .btn-filter.approve {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }
    .btn-filter.active {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }
    .btn-filter.ack {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }
    .btn-filter.reject {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }

    .btn-filter.done {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }

    @media (max-width: 576px) {
        .btn-filter {
            flex: 1 1 100%;
        }
    }
</style>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>LIST DATA USULAN KARYAWAN</b>
        </div>
    </legend>
    <div style="display:flex; flex-wrap:wrap; gap:10px;">
        <button class="btn-filter active" onclick="fr.filter(this, event, 1)">DRAFT</button>
        <button class="btn-filter ack" onclick="fr.filter(this, event, 2)">ACKNOWLEDGE</button>
        <button class="btn-filter approve" onclick="fr.filter(this, event, 3)">APPROVED</button>
        <button class="btn-filter reject" onclick="fr.filter(this, event, 4)">REJECT</button>
        <button class="btn-filter done" onclick="fr.filter(this, event, 6)">DONE</button>
    </div>
    <br>
    <div class="col-xs-12 no-padding list_data" >
        <div class="spinner-load"></div>
    </div>
</fieldset>


