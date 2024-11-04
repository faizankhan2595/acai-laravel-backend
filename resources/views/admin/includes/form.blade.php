<script src="{{ asset('adminassets/plugins/ckeditor5/build/ckeditor.js') }}"></script>
<script src="{{ asset('adminassets/plugins/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}"></script>
<script>
    if(document.querySelector( '.editor' ) != undefined){
        ClassicEditor
                .create( document.querySelector( '.editor' ), {

                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'link',
                            'alignment',
                            // 'mediaEmbed',
                            'fontColor',
                            'fontBackgroundColor',
                            'fontSize',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'blockQuote',
                            'strikethrough',
                            'horizontalLine',
                            'indent',
                            'outdent',
                            '|',
                            // 'imageUpload',
                            'highlight',
                            'insertTable',
                            'undo',
                            'redo'
                        ]
                    },
                    language: 'en',
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:full',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    },
                    licenseKey: '',

                } )
                .then( editor => {
                    window.editor = editor;
                } )
                .catch( error => {
                    console.error( 'Oops, something gone wrong!' );
                    console.error( 'Please, report the following error in the https://github.com/ckeditor/ckeditor5 with the build id and the error stack trace:' );
                    console.warn( 'Build id: m7hx01sx3cqz-cg0fptwk2612' );
                    console.error( error );
                } );
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('form').parsley();
    });
    // $(function () {
    //     $('#demo-form').parsley().on('field:validated', function () {
    //         var ok = $('.parsley-error').length === 0;
    //         $('.alert-info').toggleClass('hidden', !ok);
    //         $('.alert-warning').toggleClass('hidden', ok);
    //     })
    //     .on('form:submit', function () {
    //         return false; // Don't submit form for this demo
    //     });
    // });
</script>
