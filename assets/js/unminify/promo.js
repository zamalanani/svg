let url, promo = $("#promo").DataTable({
    responsive: true,
    scrollX: true,
    ajax: readUrl,
    columnDefs: [{
        searcable: false,
        orderable: false,
        targets: 0
    }],
    order: [
        [1, "asc"]
    ],
    columns: [{
        data: null
    }, {
        data: "nama"
    }, {
        data: "alamat"
    }, {
        data: "keterangan"
    }, {
        data: "action"
    }]
});

function reloadTable() {
    promo.ajax.reload();
}
function addData() {
    $.ajax({
        url: addUrl,
        type: "post",
        dataType: "json",
        data: $("#form").serialize(),
        success: () => {
            $(".modal").modal("hide"), Swal.fire("Sukses", "Sukses Menambahkan Data", "success"), reloadTable();
        },
        error: (a) => {
            console.log(a);
        },
    });
}
function remove(a) {
    Swal.fire({ title: "Hapus", text: "Hapus data ini?", type: "warning", showCancelButton: !0 }).then(() => {
        $.ajax({
            url: removeUrl,
            type: "post",
            dataType: "json",
            data: { id: a },
            success: () => {
                Swal.fire("Sukses", "Sukses Menghapus Data", "success"), reloadTable();
            },
            error: (a) => {
                console.log(a);
            },
        });
    });
}
function editData() {
    $.ajax({
        url: editUrl,
        type: "post",
        dataType: "json",
        data: $("#form").serialize(),
        success: () => {
            $(".modal").modal("hide"), Swal.fire("Sukses", "Sukses Mengedit Data", "success"), reloadTable();
        },
        error: (a) => {
            console.log(a);
        },
    });
}
function add() {
    (url = "add"), $(".modal-title").html("Add Data"), $('.modal button[type="submit"]').html("Add");
}
function edit(a) {
    $.ajax({
        url: get_promoUrl,
        type: "post",
        dataType: "json",
        data: { id: a },
        success: (a) => {
            $('[name="id"]').val(a.id),
                $('[name="nama"]').val(a.nama),
                $('[name="alamat"]').val(a.alamat),
                $('[name="keterangan"]').val(a.keterangan),
                $(".modal").modal("show"),
                $(".modal-title").html("Edit Data"),
                $('.modal button[type="submit"]').html("Edit"),
                (url = "edit");
        },
        error: (a) => {
            console.log(a);
        },
    });
}
promo.on("order.dt search.dt", () => {
    promo
        .column(0, { search: "applied", order: "applied" })
        .nodes()
        .each((a, e) => {
            a.innerHTML = e + 1;
        });
}),
    $("#form").validate({
        errorElement: "span",
        errorPlacement: (e, t) => {
            e.addClass("invalid-feedback"), t.closest(".form-group").append(a);
        },
        submitHandler: () => {
            "edit" == url ? editData() : addData();
        },
    }),
    $(".modal").on("hidden.bs.modal", () => {
        $("#form")[0].reset(), $("#form").validate().resetForm();
    });
