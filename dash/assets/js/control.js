var control =
    {
        callModal: function(filename, filepath)
        {
            $('#modalTitle').html(filename);
            var html = '<img src="' + filepath + '" alt="example" style="width: 100%">';
            html += '<h4>Ссылка на изображение:</h4>';
            html += '<h5>' + filepath + '</h5>';
            html += '<form method="post">';
            html += '<input type="hidden" value="' + filename + '" name="filename">';
            html += '<div class="form-group">';
            html += '<label for="modalDimensions">Создать копию с разрешением</label>';
            html += '<input type="text" class="form-control" id="modalDimensions" name="dimensions" placeholder="ширина X высота"></div>';
            html += '</form>';
            html += '<input type="button" value="Удалить" onclick="control.requestDelete(\'' + filename + '\')" class="btn btn-danger">';
            $('#modalBody').html(html);
            $('#dashModal').modal('show');
        },
        requestDelete: function(filename)
        {
            $.ajax({
                type: "POST",
                url: "index.php?section=dash&edit=files",
                data:({to_delete:filename}),
                success: function()
                {
                    window.location.reload();
                }
            });
        },
        submit: function()
        {
            var data = $('form').serialize();
            $.ajax({
                type: "POST",
                url: "index.php?section=dash&edit=files&resize=1",
                data: data,
                success: function()
                {
                    window.location.reload();
                }
            });
        }
    };