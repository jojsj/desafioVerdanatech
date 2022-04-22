window.onload = (event) => {
    const dataTable = new DataTable("#tabela_chamados", {
        responsive: true,
        order: [],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
        },
        lengthMenu: [
            [5, 10, 20, -1],
            [5, 10, 20, "Todos"]
        ],
        pageLength: 5,
        ajax: function(data, callback, settings) {
            carregarChamados().then(chamados => {
                let response = {};
                response.data = [];
                response.data = Object.keys(chamados).map(function(key) {
                    return chamados[key]
                });
                callback(response);
            })
        },
        columns: [
            { "data": "titulo" },
            { "data": "descricao" },
            { "data": "solicitante" },
            { "data": "statusLabel" },
            {
                data: null,
                orderable: false
            },
        ],

        columnDefs: [{
            targets: 4,
            render: function(data, type, row, meta) {
                let buttons = `
                <div class="table_buttons_container"> 
                    <i class="fa fa-pencil edit" id="edit_${ row.id }"></i>
                    <i class="fa-solid fa-xmark delete" id="delete_${ row.id }"></i>
                </div>
                `;
                return buttons;
            }

        }],
    });

    $('#tabela_chamados tbody').on('click', '.edit', function() {
        let id = $(this).attr("id").match(/\d+/)[0];
        editarChamado(id);
    });


    $('#tabela_chamados tbody').on('click', '.delete', function() {
        var id = $(this).attr("id").match(/\d+/)[0];
        excluirChamado(id);
    });


    async function carregarChamados() {
        return fetch("/server/api/index.php/chamados", { method: 'GET' }).then(response => {
            if (response.ok) {
                return response.json().then(data => {
                    data.forEach(chamado => {
                        chamado.statusLabel = Number(chamado.status) === 1 ? "Aberto" : "Fechado";
                    });
                    return data;
                });
            } else {
                toastr.error("Nenhum chamado encontrado", "Oops...");
                return [];
            }
        }).catch(error => {
            toastr.error("Falha na conexão", error.message);
            return [];
        });
    }


    function editarChamado(id) {
        window.location = './index.html?id=' + id;
    }

    function excluirChamado(id) {
        let result = confirm("Tem certeza que deseja excluir este chamado?");
        if (result) {
            fetch('/server/api/index.php/chamado/' + id, {
                method: 'DELETE',
            }).then(res => {
                if (res.ok) {
                    res.json().then(res => {
                        toastr.success(res.message, 'Sucesso!');
                        dataTable.ajax.reload();
                    });
                } else {
                    toastr.error("Nenhum chamado encontrado", "Oops...");
                }
            });
        }
    }
};
