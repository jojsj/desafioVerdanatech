window.onload = (event) => {

    function getAllUrlParams(url) {
        var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
        var obj = {};
        if (queryString) {
            queryString = queryString.split('#')[0];
            var arr = queryString.split('&');
            for (var i = 0; i < arr.length; i++) {
                var a = arr[i].split('=');
                var paramName = a[0];
                var paramValue = typeof(a[1]) === 'undefined' ? true : a[1];
                paramName = paramName.toLowerCase();
                if (typeof paramValue === 'string') paramValue = paramValue.toLowerCase();
                if (paramName.match(/\[(\d+)?\]$/)) {
                    var key = paramName.replace(/\[(\d+)?\]/, '');
                    if (!obj[key]) obj[key] = [];
                    if (paramName.match(/\[\d+\]$/)) {
                        var index = /\[(\d+)\]/.exec(paramName)[1];
                        obj[key][index] = paramValue;
                    } else {
                        obj[key].push(paramValue);
                    }
                } else {
                    if (!obj[paramName]) {
                        obj[paramName] = paramValue;
                    } else if (obj[paramName] && typeof obj[paramName] === 'string') {
                        obj[paramName] = [obj[paramName]];
                        obj[paramName].push(paramValue);
                    } else {
                        obj[paramName].push(paramValue);
                    }
                }
            }
        }
        return obj;
    }

    function fixDate(data) {
        let day = new Date(data);
        let date = day.getFullYear() + '-' +
            (day.getMonth() + 1).toString().padStart(2, '0') + '-' +
            day.getDate().toString().padStart(2, '0');
        let time = day.getHours().toString().padStart(2, '0') + ':' + day.getMinutes().toString().padStart(2, '0');
        return date + 'T' + time;
    }

    function fillForm(params) {
        document.getElementById("titulo").value = params.titulo;
        document.getElementById("descricao").value = params.descricao;
        document.getElementById("status").value = params.status;
        document.getElementById("data_abertura").value = fixDate(params.data_abertura);
        document.getElementById("solicitante").value = params.solicitante;
        if (params.id) {
            document.getElementById("id").value = params.id;
        }
    }

    function getChamadoForm() {
        let chamado = {};
        chamado.titulo = document.getElementById("titulo").value;
        chamado.descricao = document.getElementById("descricao").value;
        chamado.status = document.getElementById("status").value;
        chamado.data_abertura = document.getElementById("data_abertura").value;
        chamado.solicitante = document.getElementById("solicitante").value;
        if (document.getElementById("id").value) {
            chamado.id = document.getElementById("id").value;
        }
        return chamado;
    }

    function submitChamado(chamado) {
        let baseurl = '/server/api/index.php/chamado/';
        let method = chamado.id ? 'PUT' : 'POST';
        let url = method === 'PUT' ? baseurl + chamado.id : baseurl;
        fetch(url, {
            method: method,
            body: JSON.stringify(chamado)
        }).then(response => response.json().then(res => {

            toastr.success(res.message, 'Sucesso!', {
                timeOut: 1000,
                progressBar: true,
                onHidden: () => {
                    window.location = './listar.html';
                }
            });
        })).catch(error => {
            toastr.error("Erro ao abrir chamado", error.message);
        });
    }

    let parameters = getAllUrlParams(window.location.href);

    if (parameters.id) {
        document.getElementById("page_title").innerText = "Editar chamado";
        if (parameters.titulo && parameters.descricao &&
            parameters.status && parameters.data_abertura &&
            parameters.solicitante) {
            fillForm(parameters);
        } else {
            fetch('/server/api/index.php/chamado/' + parameters.id, {
                method: 'GET'
            }).then(response => {
                if (response.ok) {
                    response.json().then(data => {
                        fillForm(data);
                    });
                } else {
                    toastr.error("Chamado não encontrado", "Falha na conexão");
                }
            }).catch(error => {
                toastr.error("Falha na conexão", error.message);
            });
        }
    } else {
        document.getElementById("page_title").innerText = "Abrir chamado";
    }

    let form = document.getElementById('form_chamado');
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        let chamado = getChamadoForm();
        submitChamado(chamado);
    });

};
