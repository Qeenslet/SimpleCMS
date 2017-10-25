var control =
    {
        callModal: function(filename, filepath)
        {
            $('#modalTitle').html(filename);
            var $modal = $('#modalBody');
            $modal.html('');
            var $div = $('<div>');
            var $img = $("<img>", {load: function()
            {
                $('#dashModal').modal('show');
                var $w = $('#modalWidth');
                var $h = $('#modalHeight');
                $w.val(this.width);
                $h.val(this.height);
                if ($w.val())
                {
                    $h.data('prop', $h.val() / $w.val());
                }
                if (this.height)
                {
                    $w.data('prop', $w.val() / $h.val());
                }
                $('#dimensionsHidden').val(this.width + 'X' + this.height);
            },
            src: filepath,
            alt: 'example',
            width: '100%',
            id: "theImage"});
            $div.append($img);
            $div.attr('id', 'imgcont');
            $div.css({overflow: "hidden"});
            var control = '<div class="trashCan"><span onclick="control.requestDelete(\'' + filename + '\')" class="glyphicon glyphicon-trash" title="Удалить"></span></div>';
            $div.append(control);
            var html = '';
            html += '<h4>Ссылка на изображение:</h4>';
            html += '<h5>' + filepath + '</h5>';
            html += '<h3>Редактирование</h3><h4>Вращение</h4>';
            html += '<div style="text-align: center; margin-bottom: 10px">';
            html += '<button type="button" onclick="control.rotate(0)"><i class="fa fa-undo" aria-hidden="true"></i></button>';
            html += '<button type="button" onclick="control.rotate(1)"><i class="fa fa-repeat" aria-hidden="true"></i></button></div>';
            html += '<div style="width: 50%;">';
            html += '<h4>Изменение размеров</h4><form method="post">';
            html += '<input type="hidden" value="' + filename + '" name="filename">';
            html += '<div class="form-group">';
            html += '<label class="col-sm-6" for="modalWidth">Ширина</label>';
            html += '<div class="col-sm-6 input-group">';
            html += '<span class="input-group-btn"><input type="button" class="btn btn-default" value="-" onclick="control.manipulate(\'w\', 0)"></span> ';
            html += '<input type="text" class="form-control" id="modalWidth" name="dWidth" onchange="control.link(\'w\')">';
            html += '<span class="input-group-btn"><input type="button" class="btn btn-default" value="+" onclick="control.manipulate(\'w\', 1)"></span> ';
            html += '</div>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label class="col-sm-6" for="modalHeight">Высота</label>';
            html += '<div class="col-sm-6 input-group">';
            html += '<span class="input-group-btn"><input type="button" class="btn btn-default" value="-" onclick="control.manipulate(\'h\', 0)"></span> ';
            html += '<input type="text" class="form-control" id="modalHeight" name="dHeight" onchange="control.link(\'h\')">';
            html += '<span class="input-group-btn"><input type="button" class="btn btn-default" value="+" onclick="control.manipulate(\'h\', 1)"></span> ';
            html += '</div>';
            html += '</div>';
            html += '<input type="hidden" name="dimensions" value="" id="dimensionsHidden">';
            html += '<input type="hidden" name="rotations" value="0" id="rotationsHidden">';
            html += '</form>';
            html += '</div>';
            $modal.append($div);
            $modal.append(html);
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
        },

        manipulate: function(dest, dir)
        {
            if (dest == 'w')
            {
                var $destination = $('#modalWidth')
            }
            else if (dest == 'h')
            {
                $destination = $('#modalHeight');
            }
            if ($destination)
            {
                var value = $destination.val();
                value = parseInt(value);
                if (dir) value++;
                else value--;
                $destination.val(value);
                this.link(dest);
            }
        },

        link: function(dest)
        {
            if (dest == 'w')
            {
                var $master = $('#modalWidth');
                var $slave = $('#modalHeight');
            }
            else if (destt == 'h')
            {
                $master = $('#modalHeight');
                $slave = $('#modalWidth');
            }
            if ($master && $slave)
            {
                var value1 = $master.val();
                var prop = $master.data('prop');
                prop = parseFloat(prop);
                if (prop)
                {
                    var value2 = value1 / prop;
                    value2 = Math.round(value2);
                    $slave.val(value2);
                    $('#dimensionsHidden').val(value1 + 'X' + value2);
                }
            }
        },
        rotate: function (dir)
        {
            var $rotation = $('#rotationsHidden');
            var $image = $('#theImage');
            var angle = $rotation.val();
            angle = parseInt(angle);
            if (angle)
            {
                var key = 'rotate' + angle;
                $image.removeClass(key);
            }
            var newAngle = 0;
            if (dir)
            {
                newAngle = angle + 90;
            }
            else
            {
                newAngle = angle - 90;
            }
            if (newAngle == 360) newAngle = 0;
            if (newAngle == -90) newAngle = 270;
            $rotation.val(newAngle);
            if (newAngle)
            {
                var key2 = 'rotate' + newAngle;
                $image.addClass(key2);
            }

        }
    };