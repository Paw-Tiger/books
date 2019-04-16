$(document).ready(function () {
    var base = 'api';
    getData();

    function deleteData() {
        $(".deleteButton").click(function (e) {
            var input = $(this).data('id');
            $.ajax({
                type: "DELETE",
                url: base + "/books/"+input,
                success: function (response) {
                    alert(response);
                    getData();
                }
            });
        });
    }

    function getData() {
        $.ajax({
            type: "GET",
            url: base + "/books",
            // data: "name=John&location=Boston",
            success: function (response) {
                var trHTML = '';
                // trHTML+=$('#records_table tr').outerHTML;
                $('#records_table tbody').empty();
                $.each(response, function (i, item) {
                    trHTML += '<tr><td>' + item.id + '</td><td>' + item.name + '</td><td>' + item.author + '</td><td>' + item.shortDescription + '</td><td>' +
                        '<button class="btn btn-warning" data-id="' + item.id + '" data-toggle="modal" data-target="#exampleModal">Редактировать</button> ' +
                        '<button class="btn btn-danger deleteButton" data-id="' + item.id + '">Удалить</button>' +
                        '</td></tr>';
                });
                $('#records_table tbody').append(trHTML);
                deleteData();
            }
        });
    }

    let serialize = function (obj) {
        var str = [];
        for (var p in obj)
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        return str.join("&");
    };
    var bookId;
    $('#exampleModal').on('show.bs.modal', function (e) {
        $('#editName').val('');
        $('#editAuthor').val('');
        $('#editDescription').val('');
        bookId = $(e.relatedTarget).data('id');

        if(bookId) {
            let url = base + "/books/" + bookId ;
            let type = "GET";
            $.ajax({
                type: type,
                url: url,
                success: function (response) {
                    $('#editName').val(response.name);
                    $('#editAuthor').val(response.author);
                    $('#editDescription').val(response.shortDescription);
                }
            });
        }

    });


    $('#editButton').click(function (e) {
        let url, type;
        let params = {
            name: $('#editName').val(),
            author: $('#editAuthor').val(),
            shortDescription: $('#editDescription').val(),
        };
        // console.log(serialize(params))
        if (bookId) {
            url = base + "/books/" + bookId + '?' + serialize(params);
            type = "PUT";
        } else {
            url = base + "/books";
            type = "POST";
        }
        $.ajax({
            type: type,
            url: url,
            data: serialize(params),
            success: function (response) {
                alert(response);
                getData();
                $('#exampleModal').modal('hide');
                bookId = undefined;
            }
        });
    })

});