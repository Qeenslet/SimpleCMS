var container;
$( function()
{
    container = $('#myfiles').mosaicflow();
    $( window ).resize(function()
    {
        container.mosaicflow('refill');
    });
    var mosaicManipulator =
        {
            create: function (images)
            {
                for (var i in images)
                {
                    var src = images[i].src;
                    var file = images[i].filename;
                    var src2 = images[i].src2;
                    var w = 300 * Math.random();
                    var h = 400 * Math.random();
                    var prop = images[i].proportion;
                    if (prop)
                    {
                        if (prop < 1)
                        {
                            h = prop * w
                        }
                        else
                        {
                            w = prop * h;
                        }
                    }
                    w = Math.round(w);
                    h = Math.round(h);
                    console.log(images[i]);
                    if (src && file )
                    {
                        var html = this.makeDiv(src, file, src2);
                        var elm = $(html);
                        container.mosaicflow('add', elm);
                    }
                }
            },
            makeDiv: function (src, file, src2) {
                var html = '<div class="mosaicflow__item">' +
                    '<img src="' + src + '" alt="uploaded" onclick="control.callModal(\'' + file + '\', \'' + src2 + '\')" style="cursor: pointer" width="230px" height="140px">\n' +
                    '</div>';
                return html;
            }
        };

    // feature detection for drag&drop upload

    var isAdvancedUpload = function()
    {
        var div = document.createElement( 'div' );
        return ( ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div ) ) && 'FormData' in window && 'FileReader' in window;
    }();


    // applying the effect for every form

    $( '.box' ).each( function()
    {
        var $form		 = $( this ),
            $input		 = $form.find( 'input[type="file"]' ),
            $label		 = $form.find( 'label' ),
            $errorMsg	 = $form.find( '.box__error span' ),
            droppedFiles = false,
            showFiles	 = function( files )
            {
                $label.text( files.length > 1 ? ( $input.attr( 'data-multiple-caption' ) || '' ).replace( '{count}', files.length ) : files[ 0 ].name );
            };

        // letting the server side to know we are going to make an Ajax request
        $form.append( '<input type="hidden" name="ajax" value="1" />' );

        // automatically submit the form on file select
        $input.on( 'change', function( e )
        {
            showFiles( e.target.files );


            $form.trigger( 'submit' );


        });


        // drag&drop files if the feature is available
        if( isAdvancedUpload )
        {
            $form
                .addClass( 'has-advanced-upload' ) // letting the CSS part to know drag&drop is supported by the browser
                .on( 'drag dragstart dragend dragover dragenter dragleave drop', function( e )
                {
                    // preventing the unwanted behaviours
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on( 'dragover dragenter', function() //
                {
                    $form.addClass( 'is-dragover' );
                })
                .on( 'dragleave dragend drop', function()
                {
                    $form.removeClass( 'is-dragover' );
                })
                .on( 'drop', function( e )
                {
                    droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
                    showFiles( droppedFiles );


                    $form.trigger( 'submit' ); // automatically submit the form on file drop


                });
        }


        // if the form was submitted

        $form.on( 'submit', function( e )
        {

            // preventing the duplicate submissions if the current one is in progress
            if( $form.hasClass( 'is-uploading' ) ) return false;
            $('.box__error').hide();
            $form.addClass( 'is-uploading' ).removeClass( 'is-error' );

            if( isAdvancedUpload ) // ajax file upload for modern browsers
            {
                e.preventDefault();

                // gathering the form data
                var ajaxData = new FormData( $form.get( 0 ) );
                if( droppedFiles )
                {
                    $.each( droppedFiles, function( i, file )
                    {
                        ajaxData.append( $input.attr( 'name' ), file );
                    });
                }

                // ajax request
                $.ajax(
                    {
                        url: 			$form.attr( 'action' ),
                        type:			$form.attr( 'method' ),
                        data: 			ajaxData,
                        dataType:		'json',
                        cache:			false,
                        contentType:	false,
                        processData:	false,
                        complete: function()
                        {
                            $form.removeClass( 'is-uploading' );
                        },
                        success: function( data )
                        {
                            if (data.success)
                            {
                                $form.addClass('is-success');
                            }
                            else
                            {
                                $form.addClass('is-error');
                            }
                            if( !data.success ) {
                                $errorMsg.text( data.error );
                                $('.box__error').show();
                            }
                            else
                            {
                                mosaicManipulator.create(data.images);
                                container.mosaicflow('refill');
                            }

                        },
                        error: function(xhr, status, error)
                        {
                            alert('Произошла ошибка на сервере!');
                        }
                    });
            }
            else // fallback Ajax solution upload for older browsers
            {
                var iframeName	= 'uploadiframe' + new Date().getTime(),
                    $iframe		= $( '<iframe name="' + iframeName + '" style="display: none;"></iframe>' );

                $( 'body' ).append( $iframe );
                $form.attr( 'target', iframeName );

                $iframe.one( 'load', function()
                {
                    var data = $.parseJSON( $iframe.contents().find( 'body' ).text() );
                    $form.removeClass( 'is-uploading' ).addClass( data.success == true ? 'is-success' : 'is-error' ).removeAttr( 'target' );
                    if( !data.success ) $errorMsg.text( data.error );
                    $iframe.remove();
                });
            }
        });
    });

});

