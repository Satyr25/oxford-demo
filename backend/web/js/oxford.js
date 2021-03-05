var photoSwipe;
var timerSeccion;
var basePath = $('#base_path').val();

$('#tabla-instituto').on('click', '.view-colegio', function(event) {
    id = $(this).data('id');
    ciclo_escolar = $(this).data('ciclo');
    window.location.href = basePath + "/institutes/colegio?id=" + id + '&ciclo_escolar=' + ciclo_escolar
})

$('#tabla-grupos').on('click', '.view-grupo', function(event) {
    id = $(this).attr('id');
    window.location.href = "grupo?id=" + id
})

$('#tabla-alumnos').on('click', '.view-alumno', function(event) {
    id = $(this).attr('id');
    window.location.href = "alumno?id=" + id
})

$('#tabla-examenes-acad').on('click', '.view-acad-exam', function(event) {
    id = $(this).attr('id');
    window.location.href = "view-exam?id=" + id
})

$('#tabla-examenes').on('click', '.view-exam', function(event) {
    id = $(this).attr('id');
    window.location.href = "view-exam?id=" + id
})

$('#tabla-grupos-ins').on('click', '.view-grupo-ins', function(event) {
    id = $(this).attr('id');
    window.location.href = "grupo?id=" + id
})

$('#tabla-questions').on('click', '.view-reactivo', function(event){
    id = $(this).attr('id');
    window.location.href = "view-reactivo?id=" + id
})

$('#tabla-examenes-students').on('click touchstart', '.view-student-exam', function(event){
    id = $(this).attr('id');
    window.location.href = "solve-exam?id=" + id
})

$('#tabla-writing').on('click', '.view-writing-question', function(event){
    id = $(this).attr('id');
    window.location.href = $('#base_path').val()+"/score-exam/writing?id=" + id
})

$('#tabla-writing').on('click', '.view-writing-question-v2', function(event){
    id = $(this).attr('id');
    window.location.href = $('#base_path').val()+"/score-exam/writing-v2?id=" + id
})

$('.ver-writing').on('click', function(){
    var id = $(this).attr('id');
    window.location.href = "review-writing?id=" + id
});

$('#tabla-ins-solved-exams').on('click', '.view-grupos-solved-exams', function (event) {
    id = $(this).attr('id');
    ciclo_escolar = $(this).data('ciclo');
    window.location.href = "grupos?id=" + id + "&ciclo_escolar=" + ciclo_escolar
})

$('#tabla-grupos-solved-exams').on('click', '.view-alumnos-solved-exams', function (event) {
    id = $(this).attr('id');
    window.location.href = "alumnos?id=" + id
})

$('#tabla-alumnos-solved-exams').on('click', '.view-exams-solved-exams', function (event) {
    id = $(this).attr('id');
    window.location.href = "exams?id=" + id
})

$('#tabla-examenes-alumno-solved-exams').on('click', '.view-student-exam-solved', function (event) {
    id = $(this).attr('id');
    window.location.href = "view-solved-exam?id=" + id
})

$('#form-agregar-react').on('click', '#btn-add-articulo', function(){
    var formData = $('#articulo-add-form').serialize()
    $.ajax({
        url: "add-articulo",
        type: "post",
        data: formData,
        success: function (data) {
            if (data) {
                refreshSectionArticuloForm()
                document.getElementById('articulo-add-form').reset()
                alert("Documento agregado")
            }
            else {
                //alert("Something went wrong")
            }
        },
        error: function () {
            //alert("Something went wrong");
        }
    });
})

// $('#form-agregar-react').on('click', '#btn-add-audio', function(){
//     var formData = $('#audio-add-form').serialize()
//     $.ajax({
//         url: "reactivos/add-audio",
//         type: "post",
//         data: formData,
//         cache: false,
//         contentType: false,
//         processData: false,

//         // Custom XMLHttpRequest
//         xhr: function () {
//             var myXhr = $.ajaxSettings.xhr();
//             if (myXhr.upload) {
//                 // For handling the progress of the upload
//                 myXhr.upload.addEventListener('progress', function (e) {
//                     if (e.lengthComputable) {
//                         $('progress').attr({
//                             value: e.loaded,
//                             max: e.total,
//                         });
//                     }
//                 }, false);
//             }
//             return myXhr;
//         },
//         success: function (data) {
//             if (data) {
//                 // refreshSectionArticuloForm()
//                 document.getElementById('audio-add-form').reset()
//                 alert("Documento agregado")
//             }
//             else {
//                 alert("Something went wrong")
//             }
//         },
//         error: function () {
//             alert("Something went wrong");
//         }
//     });
// })

$('#tabla-alumnos').on('change', '.status-dropdown', function() {
    var confirma = confirm("¿Deseas cambiar el estado del alumno?")
    if (confirma)
    {
        // $('.loader').css("display","flex");
        var dict = {};
        var id = $(this).attr('id');
        var status = this.value;
        dict.id = id;
        dict.status = status;
        $.ajax({
            url: "status-alumno",
            type: "post",
            data: dict,
            success: function (data) {
                if(data){
                    // $('.loader').css("display","none");
                    refreshTableDataAlumno();
                    }
                else{
                    // $('.loader').css("display","none");
                    alert("Error actualizando el status")
                }
            },
            error: function () {
                // $('.loader').css("display","none");
                //alert("Something went wrong");
            }
        });
    }else {
        refreshTableDataAlumno();
        return
    }
});

$('#tabla-instituto, .inactive-institutes-pg').on('change', '.status-dropdown', function() {
    var confirma = confirm("¿Deseas cambiar el estado del instituto?")
    if (confirma)
    {
        // $('.loader').css("display","flex");
        var dict = {};
        var id = $(this).attr('id');
        var status = this.value;
        dict.id = id;
        dict.status = status;
        $.ajax({
            url: basePath + "/institutes/status-instituto",
            type: "post",
            data: dict,
            success: function (data) {
                if(data){
                    // $('.loader').css("display","none");
                    refreshTableDataInstitutos();
                    }
                else{
                    // $('.loader').css("display","none");
                    alert("Error actualizando el status")
                }
            },
            error: function () {
                // $('.loader').css("display","none");
                //alert("Something went wrong");
            }
        });
    }else {
        refreshTableDataInstitutos();
        return
    }
});

$('#tabla-examenes-acad').on('change', '.status-dropdown', function() {
    var confirma = confirm("¿Deseas cambiar el estado del examen?")
    if (confirma)
    {
        // $('.loader').css("display","flex");
        var dict = {};
        var id = $(this).attr('id');
        var status = this.value;
        dict.id = id;
        dict.status = status;
        $.ajax({
            url: "status-examen",
            type: "post",
            data: dict,
            success: function (data) {
                if(data){
                    // $('.loader').css("display","none");
                    refreshTableDataExamenAcademicos();
                    }
                else{
                    // $('.loader').css("display","none");
                    alert("Error actualizando el status")
                }
            },
            error: function () {
                // $('.loader').css("display","none");
                //alert("Something went wrong");
            }
        });
    }else {
        refreshTableDataExamen();
        return
    }
});

$('#tabla-alumnos').on('change', '.exam-dropdown', function() {
    var confirma = confirm("¿Deseas cambiar el view-exam del alumno?")
    if (confirma)
    {
        // $('.loader').css("display","flex");
        var dict = {};
        var id = $(this).attr('id');
        var examen = this.value;
        dict.id = id;
        dict.examen = examen;
        $.ajax({
            url: "examen-alumno",
            type: "post",
            data: dict,
            success: function (data) {
                if(data){
                    // $('.loader').css("display","none");
                    refreshTableDataAlumno();
                    }
                else{
                    // $('.loader').css("display","none");
                    alert("Error actualizando el examen")
                }
            },
            error: function () {
                // $('.loader').css("display","none");
                //alert("Something went wrong");
            }
        });
    }else {
        refreshTableDataAlumno();
        return
    }
});

$('#tabla-alumnos').on('change', '.level-dropdown', function() {
    var confirma = confirm("¿Deseas cambiar el view-level del alumno?")
    if (confirma)
    {
        // $('.loader').css("display","flex");
        var dict = {};
        var id = $(this).attr('id');
        var level = this.value;
        dict.id = id;
        dict.level = level;
        $.ajax({
            url: "level-alumno",
            type: "post",
            data: dict,
            success: function (data) {
                if(data){
                    // $('.loader').css("display","none");
                    refreshTableDataAlumno();
                    }
                else{
                    // $('.loader').css("display","none");
                    alert("Error actualizando el nivel")
                }
            },
            error: function () {
                // $('.loader').css("display","none");
                //alert("Something went wrong");
            }
        });
    }else {
        refreshTableDataAlumno();
        return
    }
});

$('#graficas-alumno').on('change', '.dropdown-examen-alumno', function() {
    var dict = {};
    var id = $(this).val();
    if(!id)
    {
        return;
    }
    dict.id = id;
    $.ajax({
        url: "get-calificaciones",
        type: "post",
        data: dict,
        success: function (data) {
            if(data){
                $.each(JSON.parse(data), function (index, element) {
                    switch(index){
                        case 'USE':
                            var container = $('#graf-use').parent();
                            $('#graf-use').remove();
                            container.append('<div id="graf-use" class="animate"></div>');
                            $('#graf-use').attr('data-percent', element).circliful({
                                animationStep: 5,
                                foregroundColor: '#115c33',
                                fontColor: '#115c33',
                                percentageTextSize: 32,
                                textColor: '#000000',
                                textAdditionalCss: 'font-weight: bold;',
                                backgroundBorderWidth: 11,
                                text: "Use of English",
                                textBelow: true
                            })
                            break;
                        case 'REA':
                            var container = $('#graf-reading').parent();
                            $('#graf-reading').remove();
                            container.append('<div id="graf-reading" class="animate"></div>');
                            $('#graf-reading').attr('data-percent', element).circliful({
                                animationStep: 5,
                                foregroundColor: '#1e9b57',
                                fontColor: '#1e9b57',
                                percentageTextSize: 32,
                                textColor: '#000000',
                                textAdditionalCss: 'font-weight: bold;',
                                backgroundBorderWidth: 11,
                                // percent: 75,
                                text: "Reading",
                                textBelow: true
                            })
                            break;
                        case 'LIS':
                            var container = $('#graf-listening').parent();
                            $('#graf-listening').remove();
                            container.append('<div id="graf-listening" class="animate"></div>');
                            $('#graf-listening').attr('data-percent', element).circliful({
                                animationStep: 5,
                                animationStep: 5,
                                foregroundColor: '#0f4f2c',
                                fontColor: '#0f4f2c',
                                percentageTextSize: 32,
                                textColor: '#000000',
                                textAdditionalCss: 'font-weight: bold;',
                                backgroundBorderWidth: 11,
                                // percent: 75,
                                text: "Listening",
                                textBelow: true
                            })
                            break;
                        case 'WRI':
                            var container = $('#graf-writing').parent();
                            $('#graf-writing').remove();
                            container.append('<div id="graf-writing" class="animate"></div>');
                            $('#graf-writing').attr('data-percent', element).circliful({
                                animationStep: 5,
                                foregroundColor: '#2adb7a',
                                fontColor: '#2adb7a',
                                percentageTextSize: 32,
                                textColor: '#000000',
                                textAdditionalCss: 'font-weight: bold;',
                                backgroundBorderWidth: 11,
                                // percent: 75,
                                text: "Writing",
                                textBelow: true
                            })
                            break;
                        case 'PRO':
                            var container = $('#graf-percentage').parent();
                            $('#graf-percentage').remove();
                            container.append('<div id="graf-percentage" class="animate"></div>');
                            $('#graf-percentage').attr('data-percent', element).circliful({
                                animationStep: 5,
                                foregroundColor: '#0d5a5c',
                                fontColor: '#0d5a5c',
                                percentageTextSize: 32,
                                textColor: '#000000',
                                textAdditionalCss: 'font-weight: bold;',
                                backgroundBorderWidth: 11,
                                // percent: 75,
                                text: "Percentage",
                                textBelow: true
                            })
                            break;
                    }
                });
            }
            else{
                alert("Error actualizando el status")
            }
        },
        error: function () {
            //alert("Something went wrong");
        }
    });
});

$('#tabla-instituto').on('change', '.pais-dropdown', function() {
    var confirma = confirm("¿Deseas cambiar el pais del instituto?")
    if (confirma)
    {
        // $('.loader').css("display","flex");
        var dict = {};
        var id = $(this).attr('id');
        var pais = this.value;
        dict.id = id;
        dict.pais = pais;
        $.ajax({
            url: "pais-instituto",
            type: "post",
            data: dict,
            success: function (data) {
                if(data){
                    // $('.loader').css("display","none");
                    refreshTableDataInstitutos();
                    }
                else{
                    // $('.loader').css("display","none");
                    alert("Error actualizando el status")
                }
            },
            error: function () {
                // $('.loader').css("display","none");
                //alert("Something went wrong");
            }
        });
    }else {
        refreshTableDataInstitutos()
        return
    }
});

$('#examen-dropdown-general-alumno').on('change', function(){
    if($(this).val() == '1'){
        $('#level-dropdown-general-alumno option').show();
        $('#level-dropdown-general-alumno option').prop('disabled', false);
        $('#level-dropdown-general-alumno').val('');
        $('#level-dropdown-general-alumno option').each(function(){
            if($(this).val() != '' && $(this).val() != 1){
                $(this).hide();
                $(this).prop('disabled', true);
            }
        });
    }else if($(this).val() == '4' || $(this).val() == '6'){
        $('#level-dropdown-general-alumno option').show();
        $('#level-dropdown-general-alumno option').prop('disabled', false);
        $('#level-dropdown-general-alumno').val('');
        $('#level-dropdown-general-alumno option').each(function(){
            if($(this).val() != '' && $(this).val() != 3){
                $(this).hide();
                $(this).prop('disabled', true);
            }
        });
    }else{
        $('#level-dropdown-general-alumno option').show();
        $('#level-dropdown-general-alumno option').prop('disabled', false);
    }
});

$('#alumnos-table-form').submit(function(event){
    var examen = $('#examen-dropdown-general-alumno').val();
    var nivel = $('#level-dropdown-general-alumno').val();
    if (examen) {
        if (!nivel) {
            event.preventDefault();
            alert("Selecciona un campo a modificar");
            return;
        }
    }
    if (nivel) {
        if (!examen) {
            event.preventDefault();
            alert("Selecciona un campo a modificar");
            return;
        }
    }
    var confirma = confirm("¿Deseas cambiar todos los alumnos seleccionados? Si los alumnos ya tienen un examen asignado este sera borrado");
    if(confirma){
        return;
    } else{
        event.preventDefault();
    }
});

$('.boton-add.group').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
    callbacks:{
        ajaxContentAdded: function(){
            $('#boton-guardar-grupo').on('click', function() {
               $('#boton-guardar-grupo').attr('disabled');
               // $('.loader').css("display","flex");
               var form = $('#grupo-add-form');
               var formData = form.serialize();
               $.ajax({
                   url: "save-group",
                   type: "post",
                   data: formData,
                   success: function (data){
                       if(data){
                           // $('.loader').css("display","none");
                           $('.mfp-close').click();
                           refreshTableDataGrupos();
                       }
                       else{
                           //alert("Something went wrong")
                           $('#boton-guardar-cliente').removeAttr('disabled');
                       }
                   },
                   error: function () {
                       // $('.loader').css("display","none");
                       //alert("Something went wrong");
                       $('#boton-guardar-cliente').removeAttr('disabled');
                   }
               });
            })
        }
    }
});

$('.boton-add.institute').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
});

$('.boton-add.alumno').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
    callbacks:{
        ajaxContentAdded: function(){
            $('#boton-guardar-alumno').on('click', function() {
               $('#boton-guardar-alumno').attr('disabled');
               // $('.loader').css("display","flex");
               var form = $('#student-add-form');
               var formData = form.serialize();
               $.ajax({
                   url: "save-student",
                   type: "post",
                   data: formData,
                   success: function (data){
                       if(data){
                           // $('.loader').css("display","none");
                           $('.mfp-close').click();
                           refreshTableDataAlumno();
                       }
                       else{
                           //alert("Something went wrong")
                           $('#boton-guardar-alumno').removeAttr('disabled');
                       }
                   },
                   error: function () {
                       // $('.loader').css("display","none");
                       //alert("Something went wrong");
                       $('#boton-guardar-alumno').removeAttr('disabled');
                   }
               });
            })
        }
    }
});

$('.boton-add.academico').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
});

$('.boton-add.admin').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
    callbacks:{
    }
});

$('.boton-add.exam').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
    callbacks:{
        ajaxContentAdded: function(){
        }
    }
});

 $('.boton-add.question').on('click', function(){
     $.ajax({
            url: $('#base_path').val()+'/questions/add-question',
            type: 'post',
            beforeSend: function(){
                $('#popup-question .contenido').html(
                    '<div class="lds-dual-ring" style="margin:0 auto;"></div>'
                );
                jQuery.magnificPopup.open({
                    items: {
                        src: jQuery('#popup-question'),
                        type: 'inline'
                    },
                    midClick: true,
                    removalDelay: 300,
                    fixedContentPos: true,
                    closeOnContentClick: false,
                    closeOnBgClick: false,
                    showCloseBtn: true,
                    enableEscapeKey: true,
                    mainClass: 'my-mfp-zoom-in'
                });
            },
            success: function(resultado){
                 $('#popup-question .contenido').html(resultado);
                 var tipo = '';
                 $('.agrega-pregunta').on('change', '#select-category', function () {
                     tipo = $(this).val();
                     switch(tipo){
                         case 'USE':
                             $('.add-use').removeClass("oculto")
                             $('.add-listening').addClass("oculto")
                             $('.add-reading').addClass("oculto")
                             $('.add-writing').addClass("oculto")
                             break
                         case 'REA':
                             $('.add-use').addClass("oculto")
                             $('.add-listening').addClass("oculto")
                             $('.add-reading').removeClass("oculto")
                             $('.add-writing').addClass("oculto")
                             break;
                         case 'LIS':
                             $('.add-use').addClass("oculto")
                             $('.add-listening').removeClass("oculto")
                             $('.add-reading').addClass("oculto")
                             $('.add-writing').addClass("oculto")
                             break;
                         case 'WRI':
                             $('.add-use').addClass("oculto")
                             $('.add-listening').addClass("oculto")
                             $('.add-reading').addClass("oculto")
                             $('.add-writing').removeClass("oculto")
                             break;
                     }
                 });

                 $('#reading-add-form').on('beforeSubmit', function (e) {
                     if (!$('select[name="ReadingForm[reading]"]').val() && !$('[name="ReadingForm[nombre]"]').val()){
                         alert('Select an article')
                         return false
                     }
                 })

                 $('#readingform-reading').on('change', function(){
                     if($(this).val() != ''){
                         $('.field-readingform-nombre').hide();
                         $('.field-readingform-texto').hide();
                     }else{
                         $('.field-readingform-nombre').show();
                         $('.field-readingform-texto').show();
                     }
                 });

                 $('#listeningform-audio_guardado').on('change', function(){
                     if($(this).val() != ''){
                         $('.field-listeningform-nombre').hide();
                         $('.field-listeningform-audio').hide();
                     }else{
                         $('.field-listeningform-nombre').show();
                         $('.field-listeningform-audio').show();
                     }
                 });

                 $('#readingform-examen').on('change', function(){
                     var examen = $('#readingform-examen').val();
                     if(examen != ''){
                         $.ajax({
                             url: $('#base_path').val()+'/questions/get-section',
                             type: "post",
                             data: {
                                 tipo,examen
                             },
                             beforeSend: function(){
                                 $('#reading-add-form input, #reading-add-form select, #reading-add-form textarea').prop('disabled', true);
                             },
                             success: function (respuesta) {
                                 if(respuesta != '0'){
                                     $('#readingform-reading').val(respuesta).change();
                                     $('.field-readingform-nombre').hide();
                                     $('.field-readingform-texto').hide();
                                     $('.field-readingform-imagen').hide();
                                     $('#reading-add-form input, #reading-add-form select, #reading-add-form textarea').prop('disabled', false);
                                     $('#article-title').text($("#readingform-reading option[value='"+respuesta+"']").text());
                                     $('.field-readingform-reading').hide();
                                 }else{
                                     $('.field-readingform-reading').show();
                                     $('#article-title').text('');
                                     $('#readingform-reading').val('');
                                     $('.field-readingform-nombre').show();
                                     $('.field-readingform-texto').show();
                                     $('.field-readingform-imagen').show();
                                     $('#reading-add-form input, #reading-add-form select, #reading-add-form textarea').prop('disabled', false);
                                 }
                             }
                         });
                     }else{
                         $('#reading-add-form input, #reading-add-form select, #reading-add-form textarea').prop('disabled', false);
                         $('.field-readingform-reading').show();
                         $('#article-title').text('');
                         $('#readingform-reading').val('');
                         $('.field-readingform-nombre').show();
                         $('.field-readingform-texto').show();
                     }
                 });

                 $('#listeningform-examen').on('change', function(){
                     var examen = $('#listeningform-examen').val();
                     if(examen != ''){
                         $.ajax({
                             url: $('#base_path').val()+'/questions/get-section',
                             type: "post",
                             data: {
                                 tipo,examen
                             },
                             beforeSend: function(){
                                 $('#listening-add-form input, #listening-add-form select, #listening-add-form textarea').prop('disabled', true);
                             },
                             success: function (respuesta) {
                                 if(respuesta != '0'){
                                     $('#listeningform-audio_guardado').val(respuesta).change();
                                     $('.field-listeningform-nombre').hide();
                                     $('.field-listeningform-audio').hide();
                                     $('#listening-add-form input, #listening-add-form select, #listening-add-form textarea').prop('disabled', false);
                                     $('#audio-title').text($("#listeningform-audio_guardado option[value='"+respuesta+"']").text());
                                     $('.field-listeningform-audio_guardado').hide();
                                 }else{
                                     $('.field-listeningform-nombre').show();
                                     $('.field-listeningform-audio').show();
                                     $('.field-listeningform-audio_guardado').show();
                                     $('#audio-title').text('');
                                     $('#listeningform-audio_guardado').val('');
                                     $('.field-listeningform-nombre').show();
                                     $('.field-listeningform-audio').show();
                                     $('#listening-add-form input, #listening-add-form select, #listening-add-form textarea').prop('disabled', false);
                                 }
                             }
                         });
                     }else{
                         $('.field-listeningform-nombre').show();
                         $('.field-listeningform-audio').show();
                         $('.field-listeningform-audio_guardado').show();
                         $('#audio-title').text('');
                         $('#listeningform-audio_guardado').val('');
                         $('.field-listeningform-nombre').show();
                         $('.field-listeningform-audio').show();
                         $('#listening-add-form input, #listening-add-form select, #listening-add-form textarea').prop('disabled', false);
                     }
                 });

                 $('#writingform-examen').on('change', function(){
                     var examen = $('#writingform-examen').val();
                     if(examen != ''){
                         $.ajax({
                             url: $('#base_path').val()+'/questions/get-section',
                             type: "post",
                             data: {
                                 tipo,examen
                             },
                             beforeSend: function(){
                                 $('#writing-add-form input, #writing-add-form select, #writing-add-form textarea').prop('disabled', true);
                                 $("#capturar-nueva").unbind( "click" );
                             },
                             success: function (respuesta) {
                                 if(respuesta != '0'){
                                     if(respuesta.tipo == 1){
                                         $('#writing-add-form input, #writing-add-form select, #writing-add-form textarea').prop('disabled', false);
                                         $('#pregunta_anterior').html(respuesta.pregunta);
                                         $('#instruccion_anterior').html(respuesta.instrucciones);
                                         $('#vista_anterior h2').text('Saved Question');
                                         $('#vista_anterior').removeClass('v2');
                                         $('#vista_anterior').show();
                                         $('.preguntas.wri').hide();
                                         $('#capturar-nueva').show();
                                         $('#capturar-nueva').on('click', function(){
                                             if(!confirm('Adding a new question will disable the last one. Continue?')){
                                                 var magnificPopup = $.magnificPopup.instance;
                                                 magnificPopup.close();
                                             }else{
                                                 $('#writing-add-form input, #writing-add-form select, #writing-add-form textarea').prop('disabled', false);
                                                 $('#vista_anterior').hide();
                                                 $('.preguntas.wri').show();
                                             }
                                         });
                                     }else{
                                         $('#writing-add-form input, #writing-add-form select, #writing-add-form textarea').prop('disabled', false);
                                         $('#pregunta_anterior').html(respuesta.pregunta1);
                                         $('#instruccion_anterior').html(respuesta.pregunta2);
                                         $('#vista_anterior h2').text('Saved Questions');
                                         $('#vista_anterior').addClass('v2');
                                         $('#capturar-nueva').hide();
                                         $('#vista_anterior').show();
                                         $('.preguntas.wri').hide();
                                     }
                                 }else{
                                     $('#writing-add-form input, #writing-add-form select, #writing-add-form textarea').prop('disabled', false);
                                     $('#vista_anterior').hide();
                                     $('.preguntas.wri').show();
                                 }
                                 $('#add-img-writing').on('click', function(){
                                     var visibles = $('.imagen-writing:visible').length;
                                     if(visibles < 5){
                                        $('#imagen-writing-'+(visibles+1)).show();
                                     }
                                 });
                             }
                         });
                     }else{
                         $('.field-listeningform-nombre').show();
                         $('.field-listeningform-audio').show();
                         $('.field-listeningform-audio_guardado').show();
                         $('#audio-title').text('');
                         $('#listeningform-audio_guardado').val('');
                         $('.field-listeningform-nombre').show();
                         $('.field-listeningform-audio').show();
                         $('#listening-add-form input, #listening-add-form select, #listening-add-form textarea').prop('disabled', false);
                     }
                 });

                 $('.add-question').on('change', '.select-tipo-react', function(){
                     var tipo = this.value;
                     if(tipo == 'MUL')
                     {
                         $(this).parent().parent().siblings('.pregunta-mult').removeClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-completa-campo').addClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-com').addClass('oculto')
                         $(this).parent().parent().siblings('.rel-columna').addClass('oculto')
                     }
                     if(tipo == 'CAM')
                     {
                         $(this).parent().parent().siblings('.pregunta-mult').addClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-completa-campo').removeClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-com').addClass('oculto')
                         $(this).parent().parent().siblings('.rel-columna').addClass('oculto')
                     }
                     if(tipo == 'REL')
                     {
                         $(this).parent().parent().siblings('.pregunta-mult').addClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-completa-campo').addClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-com').addClass('oculto')
                         $(this).parent().parent().siblings('.rel-columna').removeClass('oculto')
                     }
                     if(tipo == 'COM')
                     {
                         $(this).parent().parent().siblings('.pregunta-com').removeClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-mult').addClass('oculto')
                         $(this).parent().parent().siblings('.pregunta-completa-campo').addClass('oculto')
                         $(this).parent().parent().siblings('.rel-columna').addClass('oculto')
                     }
                 });

                 $('.btn-add-question.read').on('click', function(){
                     var renglones = $('.preguntas.read .principal.row').length
                     // renglones++
                     $('.preguntas.read').append('<div class="horizontal-separator"></div><div class="row principal horizontal-separator"> <div class="row"> <div class="col-md-12"> <div class="form-group field-readingform-instrucciones"> <label class="control-label" for="readingform-instrucciones">Instructions</label> <textarea id="readingform-instrucciones" class="form-control" name="ReadingForm[instrucciones][]" rows="1"></textarea> <p class="help-block help-block-error"></p> </div>                    </div> <div class="col-md-12"> <div class="form-group field-readingform-pregunta"> <label class="control-label" for="readingform-pregunta">Question</label> <textarea id="readingform-pregunta" class="form-control" name="ReadingForm[pregunta][]" rows="1"></textarea> <p class="help-block help-block-error"></p> </div>                    </div> </div> <div class="col-md-12"> <div class="form-group field-readingform-tipos"> <label class="control-label" for="readingform-tipos">Question Type</label> <select id="readingform-tipos" class="select-tipo-react" name="ReadingForm[tipos][]"> <option value="">Select</option> <option value="MUL">Multiple Choice</option> <option value="REL">Match Columns</option> <option value="COM">Complete Field</option></select> </select> <p class="help-block help-block-error"></p> </div>                </div> <div class="pregunta-com oculto"><h2 class="titulo-respuestas">Answers <a href="javascript:;" class="add-awnser">(add more)</a></h2><div class="row"><div class="col-md-6"><div class="form-group field-readingform-respuestascompletar"><input type="text" id="readingform-respuestascompletar'+renglones+'" class="form-control" name="ReadingForm[respuestasCompletar]['+renglones+'][]"><p class="help-block help-block-error"></p></div></div><div class="col-md-6"><div class="form-group field-readingform-respuestascompletar"><input type="text" id="readingform-respuestascompletar'+renglones+'" class="form-control" name="ReadingForm[respuestasCompletar]['+renglones+'][]"><p class="help-block help-block-error"></p></div></div></div><div class="row more-awnsers"></div></div> <div class="pregunta-mult oculto"> <div class="row"> <div class="col-md-1"> <div class="form-group field-readingform-correctosmul-'+ renglones +'"> <label class="control-label"></label> <input name="ReadingForm[correctosMul]['+ renglones +']" value="" type="hidden"><div id="readingform-correctosmul-'+ renglones +'"><div class="radio"><label><input name="ReadingForm[correctosMul]['+ renglones +']" value="a" type="radio"> </label></div> <div class="radio"><label><input name="ReadingForm[correctosMul]['+ renglones +']" value="b" type="radio"> </label></div> <div class="radio"><label><input name="ReadingForm[correctosMul]['+ renglones +']" value="c" type="radio"> </label></div></div> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-11"> <div class="form-group field-readingform-respuestasmultiple"> <label class="control-label" for="readingform-respuestasmultiple">Answer</label> <input id="readingform-respuestasmultiple" class="form-control" name="ReadingForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                            <div class="form-group field-readingform-respuestasmultiple"> <label class="control-label" for="readingform-respuestasmultiple">Answer</label> <input id="readingform-respuestasmultiple" class="form-control" name="ReadingForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                            <div class="form-group field-readingform-respuestasmultiple"> <label class="control-label" for="readingform-respuestasmultiple">Answer</label> <input id="readingform-respuestasmultiple" class="form-control" name="ReadingForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> </div> <div class="rel-columna oculto"> <div class="row"> <div class="col-md-6"> <div class="form-group field-readingform-enunciados"> <label class="control-label" for="readingform-enunciados">Sentence</label> <input id="readingform-enunciados" class="form-control" name="ReadingForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-readingform-respuestascolumna"> <label class="control-label" for="readingform-respuestascolumna">Answer</label> <input id="readingform-respuestascolumna" class="form-control" name="ReadingForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-readingform-enunciados"> <label class="control-label" for="readingform-enunciados">Sentence</label> <input id="readingform-enunciados" class="form-control" name="ReadingForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-readingform-respuestascolumna"> <label class="control-label" for="readingform-respuestascolumna">Answer</label> <input id="readingform-respuestascolumna" class="form-control" name="ReadingForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-readingform-enunciados"> <label class="control-label" for="readingform-enunciados">Sentence</label> <input id="readingform-enunciados" class="form-control" name="ReadingForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-readingform-respuestascolumna"> <label class="control-label" for="readingform-respuestascolumna">Answer</label> <input id="readingform-respuestascolumna" class="form-control" name="ReadingForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-readingform-enunciados"> <label class="control-label" for="readingform-enunciados">Sentence</label> <input id="readingform-enunciados" class="form-control" name="ReadingForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-readingform-respuestascolumna"> <label class="control-label" for="readingform-respuestascolumna">Answer</label> <input id="readingform-respuestascolumna" class="form-control" name="ReadingForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-readingform-enunciados"> <label class="control-label" for="readingform-enunciados">Sentence</label> <input id="readingform-enunciados" class="form-control" name="ReadingForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-readingform-respuestascolumna"> <label class="control-label" for="readingform-respuestascolumna">Answer</label> <input id="readingform-respuestascolumna" class="form-control" name="ReadingForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> </div> </div>')
                 });
                 $('.btn-add-question.list').on('click', function(){
                     var renglones = $('.preguntas.list .principal.row').length
                     // renglones++
                     $('.preguntas.list').append('<div class="horizontal-separator"></div><div class="principal row horizontal-separator"> <div class="row"> <div class="col-md-12"> <div class="form-group field-listeningform-instrucciones"> <label class="control-label" for="listeningform-instrucciones">Instructions</label> <textarea id="listeningform-instrucciones" class="form-control" name="ListeningForm[instrucciones][]" rows="1"></textarea> <p class="help-block help-block-error"></p> </div>                    </div> <div class="col-md-12"> <div class="form-group field-listeningform-pregunta"> <label class="control-label" for="listeningform-pregunta">Question</label> <textarea id="listeningform-pregunta" class="form-control" name="ListeningForm[pregunta][]" rows="1"></textarea> <p class="help-block help-block-error"></p> </div>                    </div> </div> <div class="col-md-12"> <div class="form-group field-listeningform-tipos"> <label class="control-label" for="listeningform-tipos">Question Type</label> <select id="listeningform-tipos" class="select-tipo-react" name="ListeningForm[tipos][]"> <option value="">Select</option> <option value="MUL">Multiple Choice</option> <option value="REL">Match Columns</option> <option value="COM">Complete Field</option></select> <p class="help-block help-block-error"></p> </div>                </div> <div class="pregunta-com oculto"><h2 class="titulo-respuestas">Answers <a href="javascript:;" class="add-awnser">(add more)</a></h2><div class="row"><div class="col-md-6"><div class="form-group field-listeningform-respuestascompletar"><input type="text" id="listeningform-respuestascompletar'+renglones+'" class="form-control" name="ListeningForm[respuestasCompletar]['+renglones+'][]"><p class="help-block help-block-error"></p></div></div><div class="col-md-6"><div class="form-group field-listeningform-respuestascompletar"><input type="text" id="listeningform-respuestascompletar'+renglones+'" class="form-control" name="ListeningForm[respuestasCompletar]['+renglones+'][]"><p class="help-block help-block-error"></p></div></div></div><div class="row more-awnsers"></div></div> <div class="pregunta-mult oculto"> <div class="row"> <div class="col-md-1"> <div class="form-group field-listeningform-correctosmul-'+ renglones +'"> <label class="control-label"></label> <input name="ListeningForm[correctosMul]['+ renglones +']" value="" type="hidden"><div id="listeningform-correctosmul-'+ renglones +'"><div class="radio"><label><input name="ListeningForm[correctosMul]['+ renglones +']" value="a" type="radio"> </label></div> <div class="radio"><label><input name="ListeningForm[correctosMul]['+ renglones +']" value="b" type="radio"> </label></div> <div class="radio"><label><input name="ListeningForm[correctosMul]['+ renglones +']" value="c" type="radio"> </label></div></div> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-11"> <div class="form-group field-listeningform-respuestasmultiple"> <label class="control-label" for="listeningform-respuestasmultiple">Answer</label> <input id="listeningform-respuestasmultiple" class="form-control" name="ListeningForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                            <div class="form-group field-listeningform-respuestasmultiple"> <label class="control-label" for="listeningform-respuestasmultiple">Answer</label> <input id="listeningform-respuestasmultiple" class="form-control" name="ListeningForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                            <div class="form-group field-listeningform-respuestasmultiple"> <label class="control-label" for="listeningform-respuestasmultiple">Answer</label> <input id="listeningform-respuestasmultiple" class="form-control" name="ListeningForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> </div> <div class="rel-columna oculto"> <div class="row"> <div class="col-md-6"> <div class="form-group field-listeningform-enunciados"> <label class="control-label" for="listeningform-enunciados">Sentence</label> <input id="listeningform-enunciados" class="form-control" name="ListeningForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-listeningform-respuestascolumna"> <label class="control-label" for="listeningform-respuestascolumna">Answer</label> <input id="listeningform-respuestascolumna" class="form-control" name="ListeningForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-listeningform-enunciados"> <label class="control-label" for="listeningform-enunciados">Sentence</label> <input id="listeningform-enunciados" class="form-control" name="ListeningForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-listeningform-respuestascolumna"> <label class="control-label" for="listeningform-respuestascolumna">Answer</label> <input id="listeningform-respuestascolumna" class="form-control" name="ListeningForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-listeningform-enunciados"> <label class="control-label" for="listeningform-enunciados">Sentence</label> <input id="listeningform-enunciados" class="form-control" name="ListeningForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-listeningform-respuestascolumna"> <label class="control-label" for="listeningform-respuestascolumna">Answer</label> <input id="listeningform-respuestascolumna" class="form-control" name="ListeningForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-listeningform-enunciados"> <label class="control-label" for="listeningform-enunciados">Sentence</label> <input id="listeningform-enunciados" class="form-control" name="ListeningForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-listeningform-respuestascolumna"> <label class="control-label" for="listeningform-respuestascolumna">Answer</label> <input id="listeningform-respuestascolumna" class="form-control" name="ListeningForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-listeningform-enunciados"> <label class="control-label" for="listeningform-enunciados">Sentence</label> <input id="listeningform-enunciados" class="form-control" name="ListeningForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-listeningform-respuestascolumna"> <label class="control-label" for="listeningform-respuestascolumna">Answer</label> <input id="listeningform-respuestascolumna" class="form-control" name="ListeningForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> </div> </div>')
                 });
                 $('.btn-add-question.use').on('click', function(){
                     var renglones = $('.preguntas.use .principal.row').length
                     // renglones++
                     $('.preguntas.use').append('<div class="horizontal-separator"></div><div class="principal row"> <div class="row"> <div class="col-md-12"> <div class="form-group field-useofenglishform-instrucciones required"> <label class="control-label" for="useofenglishform-instrucciones">Instructions</label> <textarea id="useofenglishform-instrucciones" class="form-control" name="UseOfEnglishForm[instrucciones][]" rows="1"></textarea> <p class="help-block help-block-error"></p> </div>                    </div> <div class="col-md-12"> <div class="form-group field-useofenglishform-pregunta required"> <label class="control-label" for="useofenglishform-pregunta">Question</label> <textarea id="useofenglishform-pregunta" class="form-control" name="UseOfEnglishForm[pregunta][]" rows="1"></textarea> <p class="help-block help-block-error"></p> </div>                    </div> </div> <div class="col-md-12"> <div class="form-group field-useofenglishform-tipos"> <label class="control-label" for="useofenglishform-tipos">Question Type</label> <select id="useofenglishform-tipos" class="select-tipo-react" name="UseOfEnglishForm[tipos][]"> <option value="">Select</option> <option value="MUL">Multiple Choice</option> <option value="REL">Match Columns</option> <option value="COM">Complete Field</option></select> <p class="help-block help-block-error"></p> </div>                </div> <div class="pregunta-com oculto"><h2 class="titulo-respuestas">Answers <a href="javascript:;" class="add-awnser">(add more)</a></h2><div class="row"><div class="col-md-6"><div class="form-group field-useofenglishform-respuestascompletar"><input type="text" id="useofenglishform-respuestascompletar'+renglones+'" class="form-control" name="UseOfEnglishForm[respuestasCompletar]['+renglones+'][]"><p class="help-block help-block-error"></p></div></div><div class="col-md-6"><div class="form-group field-useofenglishform-respuestascompletar"><input type="text" id="useofenglishform-respuestascompletar'+renglones+'" class="form-control" name="UseOfEnglishForm[respuestasCompletar]['+renglones+'][]"><p class="help-block help-block-error"></p></div></div></div><div class="row more-awnsers"></div></div><div class="pregunta-mult oculto"> <div class="row"> <div class="col-md-1"> <div class="form-group field-useofenglishform-correctosmul-'+ renglones +' required"> <label class="control-label"></label> <input name="UseOfEnglishForm[correctosMul]['+ renglones +']" value="" type="hidden"><div id="useofenglishform-correctosmul-'+ renglones +'"><div class="radio"><label><input name="UseOfEnglishForm[correctosMul]['+ renglones +']" value="a" type="radio"> </label></div> <div class="radio"><label><input name="UseOfEnglishForm[correctosMul]['+ renglones +']" value="b" type="radio"> </label></div> <div class="radio"><label><input name="UseOfEnglishForm[correctosMul]['+ renglones +']" value="c" type="radio"> </label></div></div> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-11"> <div class="form-group field-useofenglishform-respuestasmultiple"> <label class="control-label" for="useofenglishform-respuestasmultiple">Answer</label> <input id="useofenglishform-respuestasmultiple" class="form-control" name="UseOfEnglishForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                            <div class="form-group field-useofenglishform-respuestasmultiple"> <label class="control-label" for="useofenglishform-respuestasmultiple">Answer</label> <input id="useofenglishform-respuestasmultiple" class="form-control" name="UseOfEnglishForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                            <div class="form-group field-useofenglishform-respuestasmultiple"> <label class="control-label" for="useofenglishform-respuestasmultiple">Answer</label> <input id="useofenglishform-respuestasmultiple" class="form-control" name="UseOfEnglishForm[respuestasMultiple][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> </div> <div class="rel-columna oculto"> <div class="row"> <div class="col-md-6"> <div class="form-group field-useofenglishform-enunciados"> <label class="control-label" for="useofenglishform-enunciados">Sentence</label> <input id="useofenglishform-enunciados" class="form-control" name="UseOfEnglishForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-useofenglishform-respuestascolumna"> <label class="control-label" for="useofenglishform-respuestascolumna">Answer</label> <input id="useofenglishform-respuestascolumna" class="form-control" name="UseOfEnglishForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-useofenglishform-enunciados"> <label class="control-label" for="useofenglishform-enunciados">Sentence</label> <input id="useofenglishform-enunciados" class="form-control" name="UseOfEnglishForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-useofenglishform-respuestascolumna"> <label class="control-label" for="useofenglishform-respuestascolumna">Answer</label> <input id="useofenglishform-respuestascolumna" class="form-control" name="UseOfEnglishForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-useofenglishform-enunciados"> <label class="control-label" for="useofenglishform-enunciados">Sentence</label> <input id="useofenglishform-enunciados" class="form-control" name="UseOfEnglishForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-useofenglishform-respuestascolumna"> <label class="control-label" for="useofenglishform-respuestascolumna">Answer</label> <input id="useofenglishform-respuestascolumna" class="form-control" name="UseOfEnglishForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-useofenglishform-enunciados"> <label class="control-label" for="useofenglishform-enunciados">Sentence</label> <input id="useofenglishform-enunciados" class="form-control" name="UseOfEnglishForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-useofenglishform-respuestascolumna"> <label class="control-label" for="useofenglishform-respuestascolumna">Answer</label> <input id="useofenglishform-respuestascolumna" class="form-control" name="UseOfEnglishForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> <div class="row"> <div class="col-md-6"> <div class="form-group field-useofenglishform-enunciados"> <label class="control-label" for="useofenglishform-enunciados">Sentence</label> <input id="useofenglishform-enunciados" class="form-control" name="UseOfEnglishForm[enunciados][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> <div class="col-md-6"> <div class="form-group field-useofenglishform-respuestascolumna"> <label class="control-label" for="useofenglishform-respuestascolumna">Answer</label> <input id="useofenglishform-respuestascolumna" class="form-control" name="UseOfEnglishForm[respuestasColumna][]" type="text"> <p class="help-block help-block-error"></p> </div>                        </div> </div> </div> </div>')
                 });
            }
        });
 });

 $('.ajax-render-link').magnificPopup({
    type: 'ajax',
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
 })


$('.update-reading').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
    callbacks:{
        ajaxContentAdded: function(){
            $('#update-reading-btn').on('click', function(e){
                var id = $('#article-id').val();
                var titulo = $('#article-title').val();
                var texto = $('#article-text').val();
                if(id != "" && titulo != ""){
                    $('#article-title').prop('disabled', true);
                    $('#article-text').prop('disabled', true);
                    $('#update-reading-btn').prop('disabled', true);
                    var formData = new FormData();
                    var imagen = $('#article-image').prop('files')[0];

                    formData.append('id', id);
                    formData.append('titulo', titulo);
                    formData.append('texto', texto);
                    formData.append('imagen', imagen);
                    $.ajax({
                        url: 'update-reading',
                        type: "post",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        beforeSend: function(){
                            $('#update-reading .spinner').show();
                        },
                        success: function (respuesta) {
                            if(respuesta != '0'){
                                $('#articulo-'+id+' .titulo').text(titulo);
                                $('#articulo-'+id+' .article').text(texto);
                                if(respuesta != '1'){
                                    $('#contenedor-imagen-'+id).html(respuesta);
                                }
                                $('#mensaje-edicion').text('Reading article was updated correctly.').addClass('success');
                            }else{
                                $('#mensaje-edicion').text('There was an error updating the article.').addClass('error');
                            }
                            $('#article-title').prop('disabled', false);
                            $('#article-text').prop('disabled', false);
                            $('#update-reading-btn').prop('disabled', false);
                            $('#update-reading .spinner').hide();

                        }
                    });
                }
                e.stopPropagation();
            });
        }
    }
});

$('.update-audio').magnificPopup({
    type: 'ajax',
    midClick: true,
    removalDelay: 300,
    fixedContentPos: true,
    closeOnContentClick: false,
    closeOnBgClick: false,
    showCloseBtn: true,
    enableEscapeKey: true,
    mainClass: 'my-mfp-zoom-in',
    ajax: {
        settings: null,
        cursor: 'mfp-ajax-cur',
        tError:  'Content not found',
    },
    callbacks:{
        ajaxContentAdded: function(){
            $('#update-audio-btn').on('click', function(e){
                var id = $('#audio-id').val();
                var titulo = $('#audio-title').val();
                var formData = new FormData();
                var audio = $('#nuevo-audio').prop('files')[0];

                formData.append('id', id);
                formData.append('titulo', titulo);
                formData.append('audio', audio);
                if(id != "" && titulo != ""){
                    $('#audio-title').prop('disabled', true);
                    $.ajax({
                        url: 'update-audio',
                        type: "post",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        beforeSend: function(){
                            $('#update-reading .spinner').show();
                        },
                        success: function (respuesta) {
                            if(respuesta == '1'){
                                $('#audio-'+id+' .titulo').text(titulo);
                                $('#mensaje-edicion').text('Audio was updated correctly.').addClass('success');
                            }else{
                                $('#mensaje-edicion').text('There was an error updating the audio.').addClass('error');
                            }
                            $('#audio-title').prop('disabled', false);
                            $('#update-reading .spinner').hide();

                        }
                    });
                }
                e.stopPropagation();
            });
        }
    }
});

$('.open-inline-popup').magnificPopup({
    type:'inline',
    midClick: true
});


jQuery(document).ready(function ($) {
    checaNavbar();
    enableTooltips();

    // if($('#archivo-importado').length > 0){
    //     var id = $('#archivo-importado').val()
    //     window.location.href = "export-group?id=" + id
    // }

    $('.help-button').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,

        fixedContentPos: true,
        closeOnContentClick: false,
        closeOnBgClick: false,
        showCloseBtn: true,
        enableEscapeKey: true,
    })

    var setCookie = function (name, value, expiracy) {
        var exdate = new Date();
        exdate.setTime(exdate.getTime() + expiracy * 1000);
        var c_value = escape(value) + ((expiracy == null) ? "" : "; expires=" + exdate.toUTCString());
        document.cookie = name + "=" + c_value + '; path=/';
    };

    var getCookie = function (name) {
        var i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++) {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            if (x == name) {
                return y ? decodeURI(unescape(y.replace(/\+/g, ' '))) : y; //;//unescape(decodeURI(y));
            }
        }
    };

    $('.export-buttons').click(function () {
        $('.lds-dual-ring.exportar').toggleClass('oculto');
        setCookie('downloadStarted', 0, 100); //Expiration could be anything... As long as we reset the value
        setTimeout(checkDownloadCookie, 1000); //Initiate the loop to check the cookie.
    });
    var downloadTimeout;
    var checkDownloadCookie = function () {
        if (getCookie("downloadStarted") == 1) {
            setCookie("downloadStarted", "false", 100); //Expiration could be anything... As long as we reset the value
            $('.lds-dual-ring.exportar').toggleClass('oculto');
        } else {
            downloadTimeout = setTimeout(checkDownloadCookie, 1000); //Re-run this function in 1 second.
        }
    };

    $('#btn-import-group').on('click', function(){
        if ($('#importgrupoform-grupofile').get(0).files.length === 0 || $('.field-importgrupoform-grupofile').hasClass('has-error')) {
            return
        }
        $('#btn-import-group').toggleClass('oculto')
        $('.lds-dual-ring.importar').toggleClass('oculto')
        $('#import-grupo-form').submit()
    })

    $('form').submit(function () {
        window.onbeforeunload = null;
    });

    $('#edit-instituto-boton').on('click', function(){
        $('.show-info').toggleClass('oculto')
        $('.edit-info').toggleClass('oculto')
    })

    $('#delete-grupos-institutes').on('click',function(){
        if(confirm('You are about to delete a group and the students that are linked to it will also be deleted. Are you sure you want to continue?')){
            $('#grupos-table-form').submit()
        }
    })

    $('#edit-grupo').on('click',function(){
        $('.show-info').toggleClass('oculto')
        $('.edit-info').toggleClass('oculto')
    })

    $('#edit-student-button').on('click',function(){
        $('.info-alumno').toggleClass('oculto')
        $('.edit-info-alumno').toggleClass('oculto')
    })

    $('#boton-grade').on('click', function(){
        if (!$('input[name="WritingScoreForm[puntos]"]').val()){
            alert('Grade is empty')
            return
        }
        $('#grade-selected').text($('input[name="WritingScoreForm[puntos]"]').val());
        $.magnificPopup.open({
            items: {
                src: '#academic-grade-warning',
            },
            fixedContentPos: true,
            closeOnContentClick: false,
            closeOnBgClick: false,
            showCloseBtn: true,
            enableEscapeKey: true,
            type: 'inline'
        });
    })

    $('.finish-scoring-button').on('click', function(){
        $('#save-writing-points-form').submit();
    })

    $('.continue-scoring-button').on('click', function(){
        savePointsWriting();
    })

    $('#delete-institutes').on('click', function(){
        $('#table-form-action').val('delete');
        $('#institutes-table-form').submit()
    })

    $('#cancel-institutes').on('click', function(){
        $('#table-form-action').val('cancel');
        $('#institutes-table-form').submit()
    })

    if ($('.dropdown-examen-alumno').length > 0) {
        var val = $('.dropdown-examen-alumno').find('option').last().attr('value')
        if (val) {
            $('.dropdown-examen-alumno').val(val);
            var dict = {};
            dict.id = val;
            $.ajax({
                url: "get-calificaciones",
                type: "post",
                data: dict,
                success: function (data) {
                    if (data) {
                        $.each(JSON.parse(data), function (index, element) {
                            switch (index) {
                                case 'USE':
                                    var container = $('#graf-use').parent();
                                    $('#graf-use').remove();
                                    container.append('<div id="graf-use" class="animate"></div>');
                                    $('#graf-use').attr('data-percent', element).circliful({
                                        animationStep: 5,
                                        foregroundColor: '#115c33',
                                        fontColor: '#115c33',
                                        percentageTextSize: 32,
                                        textColor: '#000000',
                                        textAdditionalCss: 'font-weight: bold;',
                                        backgroundBorderWidth: 11,
                                        text: "Use of English",
                                        textBelow: true
                                    })
                                    break;
                                case 'REA':
                                    var container = $('#graf-reading').parent();
                                    $('#graf-reading').remove();
                                    container.append('<div id="graf-reading" class="animate"></div>');
                                    $('#graf-reading').attr('data-percent', element).circliful({
                                        animationStep: 5,
                                        foregroundColor: '#1e9b57',
                                        fontColor: '#1e9b57',
                                        percentageTextSize: 32,
                                        textColor: '#000000',
                                        textAdditionalCss: 'font-weight: bold;',
                                        backgroundBorderWidth: 11,
                                        // percent: 75,
                                        text: "Reading",
                                        textBelow: true
                                    })
                                    break;
                                case 'LIS':
                                    var container = $('#graf-listening').parent();
                                    $('#graf-listening').remove();
                                    container.append('<div id="graf-listening" class="animate"></div>');
                                    $('#graf-listening').attr('data-percent', element).circliful({
                                        animationStep: 5,
                                        animationStep: 5,
                                        foregroundColor: '#0f4f2c',
                                        fontColor: '#0f4f2c',
                                        percentageTextSize: 32,
                                        textColor: '#000000',
                                        textAdditionalCss: 'font-weight: bold;',
                                        backgroundBorderWidth: 11,
                                        // percent: 75,
                                        text: "Listening",
                                        textBelow: true
                                    })
                                    break;
                                case 'WRI':
                                    var container = $('#graf-writing').parent();
                                    $('#graf-writing').remove();
                                    container.append('<div id="graf-writing" class="animate"></div>');
                                    $('#graf-writing').attr('data-percent', element).circliful({
                                        animationStep: 5,
                                        foregroundColor: '#2adb7a',
                                        fontColor: '#2adb7a',
                                        percentageTextSize: 32,
                                        textColor: '#000000',
                                        textAdditionalCss: 'font-weight: bold;',
                                        backgroundBorderWidth: 11,
                                        // percent: 75,
                                        text: "Writing",
                                        textBelow: true
                                    })
                                    break;
                                case 'PRO':
                                    var container = $('#graf-percentage').parent();
                                    $('#graf-percentage').remove();
                                    container.append('<div id="graf-percentage" class="animate"></div>');
                                    $('#graf-percentage').attr('data-percent', element).circliful({
                                        animationStep: 5,
                                        foregroundColor: '#0d5a5c',
                                        fontColor: '#0d5a5c',
                                        percentageTextSize: 32,
                                        textColor: '#000000',
                                        textAdditionalCss: 'font-weight: bold;',
                                        backgroundBorderWidth: 11,
                                        // percent: 75,
                                        text: "Percentage",
                                        textBelow: true
                                    })
                                    break;
                            }
                        });
                    }
                    else {
                        alert("Error actualizando el status")
                    }
                },
                error: function () {
                    //alert("Something went wrong");
                }
            });
        }
    }

    if($('#solve-writing-field').length > 0){
        window.onbeforeunload = function(){
            return ''
        };
        $('#solve-writing-field').bind('copy paste cut', function (e) {
            e.preventDefault();
        });

        $('#solve-writing-field').bind('keyup', function (e) {
            var cadena = $(this).val()
            var cadenaDiv = cadena.split(' ').filter(function (n) {
                 return n != ''
                }
            )
            var num = cadenaDiv.length
            if(cadenaDiv[num-1] == ''){
                num--
            }
            $('#word-counter').html(num)
        });
    }

    // if($('.exams-student').length > 0){
    //     var aluexa = $('input[name="ExamenResueltoForm[id]"]').val()
    //     var examen = $('input[name="examen"]').val()
    //     var dict = {}
    //     dict.alumno_examen = aluexa
    //     dict.examen = examen
    //     $.ajax({
    //         url: "save-exam-random",
    //         type: "post",
    //         data: dict,
    //         success: function (data) {
    //         },
    //         error: function () {
    //             //alert("Something went wrong");
    //         }
    //     });
    // }

    if($('.writing').length > 0){
        setInterval(saveWritingPartial,10000)
    }

    $('#borrar-multiple-examen').on('click', function(){
        if($('#tabla-examenes-acad form').find('input[name="selection[]"]:checked').length > 0){
            if(confirm('You are about to delete an exam and the questions that are linked to it will also be deleted. Are you sure you want to continue?')){
                $('#tabla-examenes-acad form').submit();
            }
        }else{
            alert('Please select an exam to delete.');
        }
    });

    $('#delete-multiple-questions').on('click', function(){
        if($('#tabla-questions').find('input[name="selection[]"]:checked').length > 0){
            if(confirm('You are about to delete the selected questions. Are you sure you want to continue?')){
                $('#tabla-questions form').submit();
            }
        }else{
            alert('Please select a question to delete.');
        }
    });

    $("#edit-exam").on('click', function () {
        $(".editar").toggleClass("oculto");
        $(".ver").toggleClass("oculto");
    });

    $("#delete-exam").on('click', function (e) {
        if(confirm('You are about to delete an exam and the questions that are linked to it will also be deleted. Are you sure you want to continue?')){
            return true;
        }
        return false;
    });

    $("#edit-question").on('click', function () {
        $("#pregunta-visible").toggleClass("oculto");
        $("#editar-reactivo").toggleClass("oculto");
    });

    $("#delete-question").on('click', function (e) {
        if(confirm('Are you sure you want to delete this question?')){
            return true;
        }
        return false;
    });

    $("#btn-display-import").on('click', function () {
        $(".import-group-form").toggleClass("oculto");
    });

    $("#delete-students").on('click', function () {
        $('#status-dropdown-general-alumno option[value="INA"]').attr('selected', 'selected');
        $('#alumnos-table-form').submit();
    });

    $("#restore-students").on('click', function () {
        $('#status-dropdown-general-alumno option[value="ACT"]').attr('selected', 'selected');
        $('#alumnos-table-form').submit();
    });

    $('#show-inactive').on('click', function(){
        $('#alumnosearch-status').val(0);
        $('#filtro-status form').submit();
    });

    $('#show-active').on('click', function(){
        $('#alumnosearch-status').val(1);
        $('#filtro-status form').submit();
    });

    $("#add-section").on('click', function(){
        $('.add-sections').append('<div class="add-sections"><p class="col-md-6 nombre-campo">How many breaks:</p><p class="col-md-6"><input type="text" name="" id=""></p><p class="col-md-6 nombre-campo">Total Duration:</p><p class="col-md-6"><input type="text" name="" id=""></p><p class="col-md-6 nombre-campo">Total Points:</p><p class="col-md-6"><input type="text" name="" id=""></p><p class="col-md-6 nombre-campo">Price:</p><p class="col-md-6"><input type="text" name="" id=""></p><p class="col-md-6 nombre-campo">Status:</p><p class="col-md-6"><input type="text" name="" id=""></p><div class="col-md-12 horizontal-separator"></div></div>');
    });

    $("#graf-listening, .graf-listening").circliful({
        animationStep: 5,
        foregroundColor: '#0f4f2c',
        fontColor: '#0f4f2c',
        percentageTextSize: 32,
        textColor: '#000000',
        textAdditionalCss: 'font-weight: bold;',
        backgroundBorderWidth: 11,
        // percent: 75,
        text: "Listening",
        textBelow: true
    });

    $("#graf-reading, .graf-reading").circliful({
        animationStep: 5,
        foregroundColor: '#1e9b57',
        fontColor: '#1e9b57',
        percentageTextSize: 32,
        textColor: '#000000',
        textAdditionalCss: 'font-weight: bold;',
        backgroundBorderWidth: 11,
        // percent: 75,
        text: "Reading",
        textBelow: true
    });

    $("#graf-use, .graf-use").circliful({
        animationStep: 5,
        foregroundColor: '#115c33',
        fontColor: '#115c33',
        percentageTextSize: 32,
        textColor: '#000000',
        textAdditionalCss: 'font-weight: bold;',
        backgroundBorderWidth: 11,
        // percent: 75,
        text: "Use of English",
        textBelow: true
    });

    $("#graf-writing, .graf-writing").circliful({
        animationStep: 5,
        foregroundColor: '#2adb7a',
        fontColor: '#2adb7a',
        percentageTextSize: 32,
        textColor: '#000000',
        textAdditionalCss: 'font-weight: bold;',
        backgroundBorderWidth: 11,
        // percent: 75,
        text: "Writing",
        textBelow: true
    });

    $("#graf-speaking, .graf-speaking").circliful({
        animationStep: 5,
        foregroundColor: '#0a351e',
        fontColor: '#0a351e',
        percentageTextSize: 32,
        textColor: '#000000',
        textAdditionalCss: 'font-weight: bold;',
        backgroundBorderWidth: 11,
        text: "Speaking",
        textBelow: true
    });

    $("#graf-percentage, .graf-percentage").circliful({
        animationStep: 5,
        foregroundColor: '#0d5a5c',
        fontColor: '#0d5a5c',
        percentageTextSize: 32,
        textColor: '#000000',
        textAdditionalCss: 'font-weight: bold;',
        backgroundBorderWidth: 11,
        // percent: 75,
        text: "Percentage",
        textBelow: true
    });

    if($('.exams-student #next').length){
        var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
        $(document).on('click', '.popup-modal-dismiss', function (e) {
             nextSection();
         });
        iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
        $('#next').on('click', function(){
            var emptyQuestions = [];
            $('.preguntas-mult').each(function(i, obj) {
                if ($(obj).is(':visible')) {
                    var name = $(obj).find('input[type=radio]:first').attr('name');
                    if (!$('input[name="' + name + '"]:checked').val()) {
                        emptyQuestions.push(obj);
                    }
                }
            })
            if (emptyQuestions.length > 0) {
                $('.warning-text').removeClass('hidden');
                return;
            } else {
                $('.warning-text').addClass('hidden');
            }
            if($(this).text() == 'End Test'){
                $(this).addClass('oculto');
                $('#spinner-examen').removeClass('oculto');
                var value = $('input[name="ExamenResueltoForm[id]"]').val();
                var examen = $('input[name="examen"]').val();
                $(this).attr('href', "calificar?id=" + value + '&examen=' + examen);
            }
            else{
                if($('#seccion-USE').is(':visible')){
                    var siguiente = $('#seccion-USE .bloque-pregunta.visible').data('siguiente');
                    var numero = siguiente.split('-')[1];
                    if(numero != "NEXT" && $('#seccion-USE #USE-'+numero).length > 0){
                        $('#seccion-USE .bloque-pregunta.visible').removeClass('visible').addClass('oculto');
                        $('#seccion-USE #'+siguiente).removeClass('oculto').addClass('visible');
                    }else{
                        saveUseQuestions();
                        // $(this).text('Next Section');
                        // var siguiente_seccion = $('.seccion.visible').data('siguiente');
                        // if(siguiente_seccion != 'FIN'){
                        //     $('.seccion.visible').removeClass('visible').addClass('oculto');
                        //     $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                        //     var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                        //     iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                        // }else{
                        //     $(this).text('End Test');
                        // }
                    }
                }else{

                    if ($('#seccion-REA').is(':visible')) {
                        saveReaQuestions();
                    } else if ($('#seccion-LIS').is(':visible')) {
                        $.each($('.audio-exam'), function () {
                            $(this).get(0).pause();
                        });
                        saveLisQuestions();
                    } else if ($('#seccion-REA-1').is(':visible')) {
                        saveReaQuestions(1);
                    } else if ($('#seccion-REA-2').is(':visible')) {
                        saveReaQuestions(2);
                    } else if ($('#seccion-LIS-1').is(':visible')) {
                        saveLisQuestions(1);
                        $.each($('.audio-exam'), function () {
                            $(this).get(0).pause();
                        });
                    }
                    // var siguiente_seccion = $('.seccion.visible').data('siguiente');
                    // if(siguiente_seccion != 'FIN'){
                    //     $('.seccion.visible').removeClass('visible').addClass('oculto');
                    //     $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                    //     var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                    //     iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                    // }else{
                    //     $(this).text('End Test');
                    // }
                }
            }
        });
    }

    if ($('#questionsearch-section').length) {
        $('#search-block select').on('change', function () {
            $('#search-block form').submit();
        });
    }

    if($('#solve-writing-form').length > 0){
        iniciaContador((parseInt($('#tiempo-writing').val())), document.querySelector('#time'), (parseInt($('#tiempo-writing-used').val())));
    }

    if($('.exams-student').length > 0){
        if(navigator.onLine){
            var saveTimeInterval = setInterval(saveUsedTime, 10000)

            var checkConnectionInterval = setInterval(function () {
                fetch("../images/favicon.png").then(function (response) {
                    if (response.status !== 200) {
                        noConnectionDialog()
                        clearInterval(saveTimeInterval)
                        clearInterval(checkConnectionInterval)
                        clearInterval(timerSeccion)
                    }
                });
            }, 5000);

            window.addEventListener("offline", function() {
                noConnectionDialog()
                clearInterval(saveTimeInterval)
                clearInterval(checkConnectionInterval)
                clearInterval(timerSeccion)
            } , false);
        } else {
            noConnectionDialog()
            clearInterval(saveTimeInterval)
            clearInterval(checkConnectionInterval)
            clearInterval(timerSeccion)
        }
    }

    $('.score-speaking').on('click', function(){
        var id = $(this).attr('id');
        $('#score-speaking .lds-dual-ring').removeClass('oculto');
        $.magnificPopup.open({
            items: {
                src: '#score-speaking',
            },
            modal: false,
            type: 'inline',
            callbacks: {
                open: function() {
                    $.ajax({
                        url: $('#base_path').val()+"/score-exam/datos-speaking",
                        type: "post",
                        data: {id},
                        success: function (data) {
                            $('#score-speaking .lds-dual-ring').addClass('oculto');
                            $('#score-speaking .contenido').html(data);
                            $('#save-speaking').on('click', function(){
                                var alumno = $(this).data('alumno');
                                var calificaciones = $(this).data('calificaciones');
                                var puntos = parseInt($('#puntos-speaking').val());
                                if(!$.isNumeric(puntos) || puntos > 100){
                                    return false;
                                }
                                $.ajax({
                                    url: $('#base_path').val()+"/score-exam/guarda-speaking",
                                    type: "post",
                                    data: {id,puntos},
                                    success: function (data) {
                                        if(data == '1'){
                                            $('.score-speaking#'+id).remove();
                                            $('#score-speaking .contenido').html('<div id="saved-correctly">Speaking was scored with: <span>'+puntos+'</span></div>')
                                        }else{
                                            alert('There was an error saving the score.');
                                        }
                                    }
                                });
                            });
                        }
                    });
                },
                close: function() {
                    $('#score-speaking .lds-dual-ring').addClass('oculto');
                    $('#score-speaking .contenido').html('');
                }
            }
        });
    });

    $('#genera-certificado').on('click', function(){
        var seleccionados = [];
        var data = $(this).data('tipo');
        $('input[name="selection[]"]:checked').each(function(){
            seleccionados.push($(this).val());
        });
        if(seleccionados.length)
            window.open($('#base_url').val()+'/institutes/export-certificate?seleccion='+seleccionados.join(',')+'&tipo='+data);
        else
            alert('Select certificates to export');
    });

    $('body').on('click', '.add-awnser', function(){
        var respuesta = $(this).parent().parent().find('.col-md-6:first').clone();
        $(this).parent().parent().find('.more-awnsers').append('<div class="col-md-6">'+$(respuesta).html()+'</div>');
    });

    $('#agregar-respuesta').on('click', function(){
        var respuesta = $('#respuestas-guardadas .col-md-6:first').clone();
        $(respuesta).find('.form-control').attr('value','');
        $('#nuevas-respuestas').append('<div class="col-md-6">'+$(respuesta).html()+'</div>');
    });

    $('.zoom-reading').on('click', function(){
        var section = $(this).data('section');
        jQuery.magnificPopup.open({
            items: {
                src: jQuery('#imagen-reading-'+section),
                type: 'inline'
            },
            midClick: true,
            removalDelay: 300,
            fixedContentPos: true,
            closeOnContentClick: false,
            closeOnBgClick: false,
            showCloseBtn: true,
            enableEscapeKey: true,
            mainClass: 'imagen-reading'
        });
    });

    $('body').on('change', '.select-on-check-all', function(){
        var cookie = $('#controlador').val()+'-'+$('#accion').val();
        var seleccion = '';
        $('[name^="selection"]').each(function(){
            seleccion += ($(this).val() + ',');
        });
        seleccion = seleccion.substring(0, seleccion.length - 1);
        if($(this).is(':checked')){
            actualizaSeleccionados(cookie,seleccion,true);
        }else{
            actualizaSeleccionados(cookie,seleccion,false);
        }
    });

    $('body').not('.select-on-check-all').on('change', '[name^="selection"]',function(){
        var cookie = $('#controlador').val()+'-'+$('#accion').val();
        if($(this).is(':checked')){
            actualizaSeleccionados(cookie,$(this).val(),true);
        }else{
            actualizaSeleccionados(cookie,$(this).val(),false);
        }
    });

    $('#imprimir').on('click', function(){
        if($('.tabla-impresion form').find('input[name="selection[]"]:checked').length > 0){
            $('#impresion-form').submit()
        }else{
            alert('Selecciona alumnos a imprimir.');
        }
    })

    $('#send-writing-btn').on('click', function() {
        $.magnificPopup.open({
            items: {
                src: '#warning-dialog',
            },
            type: 'inline',
        });
    });

    $('.warning-writing-btn').on('click', function() {
        $('#solve-writing-form').submit();
    })

    $('.select-cycle').on('change', function() {
        var ciclo = $(this).val();
        $('.value-ciclo').val(ciclo);
        $('.view-colegio').attr('data-ciclo', ciclo);
        $('#link-export').attr('href', changeLastCharFromString($('#link-export').attr('href'), ciclo));
    });

    $('body').on('change', '#examenform-tipo', function(){
        var tipo = $(this).val();
        if(tipo == 1){
            $('#diagnostic-version').slideDown();
            $('#certificate-version').slideUp();
        }else if(tipo == 3){
            $('#certificate-version').slideDown();
            $('#diagnostic-version').slideUp();
            $('.field-examenform-puntos').hide();
        }else{
            $('#certificate-version').slideUp();
            $('#diagnostic-version').slideUp();
            $('.field-examenform-puntos').show();
        }
    });

    $('.audio-player-button').on('click', function() {
        $(this).parent(".audio-player-container").find(".audio-exam")[0].play();
    });

    if($('#contract-accepted').length > 0) {
        if ($('#contract-accepted').val() == 0) {
            $.magnificPopup.open({
                items: {
                    src: '#contract-popup'
                },
                type: 'inline',
                modal: true
            });
        }
    }

    if ($('#student-tutorial-popup').length > 0) {
        videojs('tutorial-en').ready(function(){
            var player = this;
            player.on('ended', function() {
                $('.exam-link-container').removeClass('hidden');
            });
        });

        videojs('tutorial-es').ready(function(){
            var player = this;
            player.on('ended', function() {
                $('.exam-link-container').removeClass('hidden');
            });
        });

        $('.btn-start-exam').on('click', function() {
            var url = $(this).data('url');
            $('#exam-link').attr('href', url);
            $.magnificPopup.open({
                items: {
                    src: '#student-tutorial-popup'
                },
                type: 'inline',
                callbacks: {
                    close: function () {
                        var en_vid = videojs('tutorial-en');
                        var es_vid = videojs('tutorial-es');
                        if (!en_vid.paused()) {
                            en_vid.pause();
                        }
                        if (!es_vid.paused()) {
                            es_vid.pause();
                        }
                    }
                }
            });
        });

        $('#student-tutorial-popup .video-select').on('click', function() {
            var language = $(this).data('language');
            $('.video-container.' + language).removeClass('hidden');
            $('.video-language-selection').addClass('hidden');
        });
    }

    $('.show-pass').on('click', function () {
        $(this).toggleClass('glyphicon-eye-close').toggleClass('glyphicon-eye-open');
        $(this).siblings('input.form-control').attr('type', ($(this).data('showing') == 0 ? 'text' : 'password'));
        $(this).data('showing', ($(this).data('showing') == 0 ? '1' : '0'));
    });

    $('#start-speaking').on('click', function() {
        var level = $('#select-level').val();
        var items = [];
        $(`.gallery.${level} a`).each(function () {
            item = {
                src: $(this).attr('href'),
                w: $(this).data('width'),
                h: $(this).data('height'),
            }
            items.push(item);
        });
        items.push({
            html: '<div class="last-slide"><h2>End of test</h2><div class="buttons-wrapper"><button type="button" class="close-photo-swipe btn-slide">Continue</button><a href="'+ $('#base_path').val() + '/exams-academic" class="btn-slide">Exit</a></div></div>'
        })
        var options = {
            loop: false,
            shareEl: false,
            arrowKeys: false
        }
        photoSwipe = new PhotoSwipe($('.pswp')[0], PhotoSwipeUI_Default, items, options);
        photoSwipe.init();
    });

    $('.pswp').on('click', '.close-photo-swipe', function() {
        photoSwipe.close();
    });

    $('#select-student-speaking-1, #select-student-speaking-2').on('change', function() {
        var selectedValue = $(this).select2("data")[0].id;
        var id = selectedValue.split('-')[0];
        var examType = selectedValue.split('-')[1];
        var container = $(this).data('container');
        $(`#${container} .table`).addClass('hidden').removeClass('visible');
//        $(`#${container} .table`).find("form").addClass('hidden').removeClass('visible');

        $(`#${container} .table.${examType}`).removeClass('hidden').addClass('visible');
//        $(`#${container} .table.${examType} > form`).removeClass('hidden').addClass('visible');
        $(`#${container}`).find('.input-student-id').val(id);
        $('.background-observations').removeClass('hidden');
    });

    var data;
    $('#submit-speaking-forms').on('click', function () {
        if (!$('#student-1 .table').hasClass('visible') && !$('#student-2 .table').hasClass('visible')) {
            alert('No student selected');
            return;
        }
        var forms = [];
        var studentFinalScores = [];
        for (var studentNumber = 1; studentNumber < 3; studentNumber++) {
            if ($(`#student-${studentNumber} .table.visible form`).length > 0)
                forms.push($(`#student-${studentNumber} .table.visible form`));
        }
        for (var i = 0; i < forms.length; i++) {
            if (!checkErrorsOnForm(forms[i])) {
                alert("All fields are required");
                return;
            }
            if (!data) {
                data = forms[i].serialize();
            } else {
                data = data + '&' + forms[i].serialize();
            }
            var scores = forms[i].find('input[type="radio"]:checked');
            var totalPoints = scores.get().reduce((total, score) => total + parseInt(score.value), 0);
            studentFinalScores.push(Math.round(((totalPoints * 100) / (scores.length * 5).toFixed(1))));
        }
        if (studentFinalScores.length < 2) {
            $('#score-confirmation .contenido #message').html(
                'Warning! You are about to score <span></span>'+studentFinalScores[0]+'</span> for <span>'+
                $('#select-student-speaking-1 option:selected').text()+'</span>'
            );
            $('#student-grades-speaking #student-1 .student-name').html($('#select-student-speaking-1 option:selected').text());
            $('#student-grades-speaking #student-1 .student-grade').html(studentFinalScores[0]);
            $.magnificPopup.open({
                items: {
                    src: '#score-confirmation',
                },
                modal: true,
                type: 'inline'
            });
        } else {
            $('#score-confirmation .contenido #message').html(
                'Warning! You are about to score <span>'+studentFinalScores[0]+'</span> for <span>'+
                $('#select-student-speaking-1 option:selected').text()+'</span> and '+
                '<span>'+studentFinalScores[1]+'</span> for <span>'+
                $('#select-student-speaking-2 option:selected').text()+'</span>'
            );
            $.magnificPopup.open({
                items: {
                    src: '#score-confirmation',
                },
                modal: true,
                type: 'inline'
            });
        }
    });

    $('#review-speaking').on('click', function(){
        var magnificPopup = $.magnificPopup.instance;
        magnificPopup.close();
    });

    $('#end-speaking').on('click', function(){
        $.ajax({
            url: 'score-new-speaking',
            type: 'POST',
            data: data
         });
    });

    $('.start-timer').on('click', function() {
        var timerContainer = $(this).data('timer');
        timerSeccion = startTimer(timerContainer);
        $(this).addClass('hidden');
        $(`.timer.${timerContainer} .timer-text`).removeClass('hidden');
    });

    var relevance = 0;
    $('.relevance').on('click', function(){
        relevance = parseInt($(this).data('percentaje'));
        $('.relevance').removeClass('selected');
        $(this).addClass('selected');
    });

    $('#save-v2').on('click', function(){
        var vocabulary = parseInt($( ".vocabulary:checked" ).val());
        var structure = parseInt($( ".structure:checked" ).val());
        var grammar = parseInt($( ".grammar:checked" ).val());
        if(isNaN(vocabulary)  || isNaN(structure) || isNaN(grammar) || relevance == 0){
            alert('Please select a score for every skill.');
            return false;
        }

        var abc = (Math.round((((vocabulary*20)+(structure*20)+(grammar*20))/300)*100));
        var wcp =  $('#wcp').val();
        var d = ((abc/100)*(wcp/100))*100;

        var total = Math.round(((d/100)*(relevance/100))*100);

        $('#grade-selected').text(total);

        $('#close-v2').on('click', function(){
            $.magnificPopup.close();
        });

        $('#submit-v2').on('click', function(){
            $.ajax({
                url: $('#base_path').val()+"/score-exam/grade-exam-v2",
                type: "post",
                data: {
                    'total': total,
                    'writing_data': $('#writing_data').val()
                },
                success: function (response) {
                    if(response == '1'){
                        window.location.href = $('#base_path').val()+'/score-exam/index-v2';
                    }else{
                        alert('There was an error grading the exam.');
                    }
                }
            });
        })

        jQuery.magnificPopup.open({
            items: {
                src: jQuery('#v2-confirm'),
                type: 'inline'
            },
            midClick: true,
            removalDelay: 300,
            fixedContentPos: true,
            closeOnContentClick: false,
            closeOnBgClick: false,
            showCloseBtn: true,
            enableEscapeKey: true,
            mainClass: 'my-mfp-zoom-in',
            closeOnContentClick: false,
            closeOnBgClick: false,
            showCloseBtn: false,
            enableEscapeKey: false,
        });
    });

    $('#ciclo-reportes').on('change', function(){
        window.location.href = $('#url').val()+$(this).val();
    });
});

function startTimer(container) {
    return setInterval(function() {
        var spentSeconds = parseInt($(`.timer.${container} .spent-seconds`).val()) + 1;
        var minutes = Math.floor(spentSeconds / 60);
        var seconds = spentSeconds % 60;
        if (minutes < 10) minutes = `0${minutes}`;
        if (seconds < 10) seconds = `0${seconds}`;
        $(`.timer.${container} .minutes`).text(minutes);
        $(`.timer.${container} .seconds`).text(seconds);
        $(`.timer.${container} .spent-seconds`).val(spentSeconds);
    }, 1000);
}

function checkErrorsOnForm(form) {
    data = form.data('yiiActiveForm');
    $.each(data.attributes, function () {
        this.status = 3;
    });
    form.yiiActiveForm("validate");
    if (form.find('.has-error').length > 0) {
        return false;
    } else {
        return true;
    }
}

function updateAudioPlayerTimes(player) {
    var length = player.duration
    var current_time = player.currentTime;

    var currentTime = calculateCurrentValue(current_time);
    $(player).parent(".audio-player-container").find(".remaining-time").html(currentTime);

    var totalLength = calculateTotalValue(length)
    $(player).parent(".audio-player-container").find(".duration-time").html(totalLength);
}

function calculateTotalValue(length) {
    var minutes = Math.floor(length / 60),
        seconds_int = length - minutes * 60,
        seconds_str = seconds_int.toString(),
        seconds = seconds_str.substr(0, 2),
        time = minutes + ':' + seconds

    return time;
}

function calculateCurrentValue(currentTime) {
    var current_hour = parseInt(currentTime / 3600) % 24,
        current_minute = parseInt(currentTime / 60) % 60,
        current_seconds_long = currentTime % 60,
        current_seconds = current_seconds_long.toFixed(),
        current_time = (current_minute < 10 ? "0" + current_minute : current_minute) + ":" + (current_seconds < 10 ? "0" + current_seconds : current_seconds);

    return current_time;
}

function actualizaSeleccionados(cookie,valor,seleccionado){
    if($.cookie(cookie) == undefined){
        var guardados = [];
    }else{
        var guardados = $.cookie(cookie).split(',');
    }
    var valores = valor.split(',');
    if(seleccionado){
        valores.forEach(function(id){
            var posicion = guardados.indexOf(id);
            if(posicion < 0){
                guardados.push(id);
            }
        });
        $.cookie(cookie, guardados.join());
    }else{
        valores.forEach(function(id){
            var posicion = guardados.indexOf(id);
            if(posicion > 0){
                guardados.splice(posicion,1);
            }
        });
        $.cookie(cookie, guardados.join());
    }
}

function noConnectionDialog(intervals) {
    $.magnificPopup.open({
        items: {
            src: '#connection-dialog',
        },
        modal: true,
        type: 'inline'
    });
    $('#next').addClass('oculto')
}

var institutes = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote:{
        url: basePath + '/institutes/get-institutes-searchbar?ciclo_escolar=' + $('.value-ciclo').val() + '&q=%QUERY',
        wildcard: '%QUERY'
    }
});

var students = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: basePath + '/institutes/get-students-searchbar?ciclo_escolar=' + $('.value-ciclo').val() + '&q=%QUERY',
        wildcard: '%QUERY'
    }
});

var institutes_solved = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote:{
        url: basePath + '/solved-exams/get-institutes-searchbar?ciclo_escolar=' + $('.value-ciclo').val() + '&q=%QUERY',
        wildcard: '%QUERY'
    }
});

var students_solved = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: basePath + '/solved-exams/get-students-searchbar?ciclo_escolar=' + $('.value-ciclo').val() + '&q=%QUERY',
        wildcard: '%QUERY'
    }
});

$('.typeahead.institutes').typeahead({
    highlight: true,
    hint: false,
    },
    {
        limit: 15,
        name: 'institutes',
        display: 'nombre',
        source: institutes,
        templates: {
            suggestion: Handlebars.compile('<p><a href="colegio?id={{id}}&ciclo_escolar=' + $('.value-ciclo').val() + '">{{nombre}}</a><span>{{tipo}}</span></p>')
        }
    },
    {
        limit: 15,
        name: 'students',
        display: 'nombre',
        source: students,
        templates: {
            suggestion: Handlebars.compile('<p><a href="alumno?id={{id}}">{{nombre}}</a><span>{{tipo}}</span></p>')
        }
    }
);

$('.typeahead.institutes').bind('typeahead:select', function (ev, suggestion) {
    switch(suggestion.tipo){
        case 'institute':
            window.location.href = 'colegio?id=' + suggestion.id + '&ciclo_escolar=' + $('.value-ciclo').val();
            break;
        case 'student':
            window.location.href = 'alumno?id=' + suggestion.id;
            break;
    }

});

$('.typeahead.institutes').bind('typeahead:autocomplete', function (ev, suggestion) {
    switch(suggestion.tipo){
        case 'institute':
            window.location.href = 'colegio?id=' + suggestion.id + '&ciclo_escolar=' + $('.value-ciclo').val();
            break;
        case 'student':
            window.location.href = 'alumno?id=' + suggestion.id;
            break;
    }

});




$('.typeahead.institutes_exam').typeahead({
    highlight: true,
    hint: false,
    },
    {
        name: 'institutes',
        display: 'nombre',
        source: institutes_solved,
        templates: {
            suggestion: Handlebars.compile('<p><a href="grupos?id={{id}}&ciclo_escolar=' + $('.value-ciclo').val() + '">{{nombre}}</a><span>{{tipo}}</span></p>')
        }
    },
    {
        name: 'students',
        display: 'nombre',
        source: students_solved,
        templates: {
            suggestion: Handlebars.compile('<p><a href="exams?id={{id}}">{{nombre}}</a><span>{{tipo}}</span></p>')
        }
    }
);

$('.typeahead.institutes_exam').bind('typeahead:select', function (ev, suggestion) {
    console.log("holo")
    switch(suggestion.tipo){
        case 'institute':
            window.location.href = 'grupos?id=' + suggestion.id  + '&ciclo_escolar=' + $('.value-ciclo').val();
            break;
        case 'student':
            window.location.href = 'exams?id=' + suggestion.id;
            break;
    }

});

$('.typeahead.institutes_exam').bind('typeahead:autocomplete', function (ev, suggestion) {
    console.log("holo")
    switch(suggestion.tipo){
        case 'institute':
            window.location.href = 'grupos?id=' + suggestion.id  + '&ciclo_escolar=' + $('.value-ciclo').val();
            break;
        case 'student':
            window.location.href = 'exams?id=' + suggestion.id;
            break;
    }

});

$('.typeahead.busqueda-impresion').typeahead({
    highlight: true,
    hint: false,
    },
    {
        name: 'students',
        display: 'nombre',
        source: students,
        templates: {
            suggestion: Handlebars.compile('<p><a href="listado?ciclo=' + $('.value-ciclo').val() + '&entidad=ALU&id={{id}}">{{nombre}}</a><span>{{tipo}}</span></p>')
        }
    },
    {
        name: 'institutes',
        display: 'nombre',
        source: institutes,
        templates: {
            suggestion: Handlebars.compile('<p><a href="listado?ciclo=' + $('.value-ciclo').val() + '&entidad=INS&id={{id}}">{{nombre}}</a><span>{{tipo}}</span></p>')
        }
    }
);

$('.typeahead.busqueda-impresion').bind('typeahead:select', function (ev, suggestion) {
    switch(suggestion.tipo){
        case 'institute':
            window.location.href = 'listado?ciclo=' + $('.value-ciclo').val() + '&entidad=INS&id=' + suggestion.id;
            break;
        case 'student':
            window.location.href = 'listado?ciclo=' + $('.value-ciclo').val() + '&entidad=ALU&id=' + suggestion.id;
            break;
    }

});

function nextSection(){
    if($('.seccion.visible').attr('id') == $('#preguntas .container .seccion').last().attr('id')){
        $('.popup-modal-dismiss').unbind('click');
        var value = $('input[name="ExamenResueltoForm[id]"]').val();
        var examen = $('input[name="examen"]').val();
        $('#time-dialog .popup-modal-dismiss').attr('href', "calificar?id=" + value + '&examen=' + examen);
        //$('#time-dialog .popup-modal-dismiss').click();
    }else{
        if($('#seccion-USE').is(':visible')){
            var siguiente = $('.seccion.visible').find('.bloque-pregunta').last().data('siguiente');
            var numero = siguiente.split('-')[1];
            if(numero != "NEXT"){
                $('#seccion-USE .bloque-pregunta.visible').removeClass('visible').addClass('oculto');
                $('#seccion-USE #'+siguiente).removeClass('oculto').addClass('visible');
            }else{
                saveUseQuestions();
                // $(this).text('Next Section');
                // var siguiente_seccion = $('.seccion.visible').data('siguiente');
                // if(siguiente_seccion != 'FIN'){
                //     $('.seccion.visible').removeClass('visible').addClass('oculto');
                //     $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                // }else{
                //     $(this).text('End Test');
                // }
            }
        }else{
            if ($('#seccion-REA').is(':visible')) {
                saveReaQuestions();
            } else if ($('#seccion-LIS').is(':visible')) {
                $.each($('.audio-exam'), function () {
                    $(this).get(0).pause();
                });
                saveLisQuestions();
            } else if ($('#seccion-REA-1').is(':visible')) {
                saveReaQuestions(1);
            } else if ($('#seccion-LIS-1').is(':visible')) {
                saveLisQuestions(1);
            }
            // var siguiente_seccion = $('.seccion.visible').data('siguiente');
            // if(siguiente_seccion != 'FIN'){
            //     $('.seccion.visible').removeClass('visible').addClass('oculto');
            //     $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
            // } else {
            //     $(this).text('End Test');
            // }
        }
    }
    $.magnificPopup.close();
    var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
    iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
}

function iniciaContador(duration, display, usedTime = 0){
    duration = duration * 60 - usedTime;
    if(duration <= 0){
        duration = 1;
    }
    $('#time').text("00:00");
    if(timerSeccion != null){
        clearInterval(timerSeccion);
    }
    var timer = duration, minutes, seconds;
    timerSeccion = setInterval(function () {
        if($('#error-dialog').is(':visible') || $('#connection-dialog').is(':visible')){
            return true;
        }
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        var texto_tiempo;
        if(minutes > 0){
            texto_tiempo = " MIN";
        }else{
            texto_tiempo = " SEC";
        }

        $('#time').text(minutes + ":" + seconds + " MIN");

        if(timer == 0){
            if($('#solve-writing-form').length){
                finalizarWriting();
            }
            $.magnificPopup.open({
                items: {
                    src: '#time-dialog',
                },
                modal: true,
                type: 'inline'
            });
            if ($('.seccion.visible').attr('id') == $('#preguntas .container .seccion').last().attr('id')){
                if($('#seccion-REA').is(':visible')){
                    saveReaQuestions();
                } else if ($('#seccion-LIS').is(':visible')){
                    $.each($('.audio-exam'), function () {
                        $(this).get(0).pause();
                    });
                    saveLisQuestions();
                } else if ($('#seccion-REA-1').is(':visible')) {
                    saveReaQuestions(1);
                } else if ($('#seccion-LIS-1').is(':visible')) {
                    saveLisQuestions(1);
                } else if ($('#seccion-USE').is(':visible')){
                    saveUseQuestions();
                }
            }
           clearInterval(timerSeccion);
        }

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

function finalizarWriting(){
    $.ajax({
        url: $('#base_path').val()+"/students/save-writing",
        type: "post",
        data: {
            'reactivo': $('#reactivo').val(),
            'alumno_examen': $('#alumno_examen').val(),
            'texto': $('#solve-writing-field').val()
        },
        success: function (data) {
        },
        error: function () {
            //alert("Something went wrong");
        }
    });
}

function checaNavbar(){
    var seleccion = $('.controller').val();
    if(seleccion == 'institutes'){
        $('#users-nav').addClass('active')
    }else if(seleccion == "exams-academic"){
        $('#see-exam-nav').addClass('active')
    }else if(seleccion == "score-exam"){
        $('#score-nav').addClass('active')
    }else if(seleccion == "groups-institute"){
        $('#groups-ins-nav').addClass('active')
    }else if(seleccion == "institute-info"){
        $('#nav-ins-info').addClass('active')
    }
}

function refreshTableDataAlumno(){
    $.pjax.reload('#pjax-grid-alumno')
}

function refreshTableDataInstitutos(){
    $.pjax.reload('#pjax-grid-institutos')
}

function refreshTableDataGrupos(){
    $.pjax.reload('#pjax-grid-grupos')
}

function refreshTableDataAcademicos(){
    $.pjax.reload('#pjax-grid-academicos')
}

function refreshSectionArticuloForm(){
    $.pjax.reload('#pjax-form-articulo')
}

function refreshTableDataExamenAcademicos(){
    $.pjax.reload('#pjax-grid-examenes-acad')
}


function saveUseQuestions(){
    var formData = new FormData();
    formData.append('ExamenResueltoForm[id]', $('input[name="ExamenResueltoForm[id]"]').val());
    $('#seccion-USE input[name="ExamenResueltoForm[preguntas][]"]').each(function () {
            formData.append('ExamenResueltoForm[preguntas][]', $(this).val());
    });
    $('#seccion-USE .preguntas-mult input:radio:checked:enabled').each(function () {
        if (!$(this).is(':disabled'))
            formData.append('ExamenResueltoForm[respuestasMul][]', $(this).val());
    });
    $('#seccion-USE .enuncia-sel').each(function () {
        formData.append('ExamenResueltoForm[enunciadosCol][]', $(this).val());
    });
    $('#seccion-USE input[name="ExamenResueltoForm[respuestasCol][]"]').each(function () {
        formData.append('ExamenResueltoForm[respuestasCol][]', $(this).val());
    });
    $('#seccion-USE input[name="ExamenResueltoForm[respuestasCom][]"]').each(function () {
        formData.append('ExamenResueltoForm[respuestasCom][]', $(this).val());
    });
    if (!formData.entries().next().value) {
        alert("Error at sending data");
        return;
    }
    $.ajax({
        url: "save-answers",
        type: "post",
        contentType: false,
        processData: false,
        data: formData,
        beforeSend: function(){
            $('#next').addClass('oculto');
            $('#spinner-examen').removeClass('oculto');
        },
        success: function (data) {
            if(data){
                var siguiente_seccion = $('.seccion.visible').data('siguiente');
                if(siguiente_seccion != 'FIN'){
                    $('.seccion.visible').removeClass('visible').addClass('oculto');
                    $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                    var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                    iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                    changeWarningTypeText(bloque_visible);
                    $(window).scrollTop(0);
                }else{
                    $('#next').text('End Test');
                }
                $('#next').removeClass('oculto');
                $('#spinner-examen').addClass('oculto');
            }else{
                $.magnificPopup.open({
                    items: {
                        src: '#error-dialog',
                    },
                    modal: true,
                    type: 'inline'
                });
                $('#next').removeClass('oculto');
                $('#spinner-examen').addClass('oculto');
                $('.retry-answers').on('click', function(){
                    var magnificPopup = $.magnificPopup.instance;
                    magnificPopup.close();
                    $('#next').trigger('click');
                });
            }
        },
        error: function (xhr, status, error) {
            $('.error-web').text('Connection error: '+xhr.status);
            $.magnificPopup.open({
                items: {
                    src: '#error-dialog',
                },
                modal: true,
                type: 'inline'
            });
            $('#next').removeClass('oculto');
            $('#spinner-examen').addClass('oculto');
            $('.retry-answers').on('click', function(){
                var magnificPopup = $.magnificPopup.instance;
                magnificPopup.close();
                $('#next').trigger('click');
            });
        }
    });
}

function saveReaQuestions(sectionNumber = 0){
    var formData = new FormData();
    if(sectionNumber == 0){
        formData.append('ExamenResueltoForm[id]', $('input[name="ExamenResueltoForm[id]"]').val());
        $('#seccion-REA input[name="ExamenResueltoForm[preguntas][]"]').each(function () {
            formData.append('ExamenResueltoForm[preguntas][]', $(this).val());
        });
        $('#seccion-REA .preguntas-mult input:radio:checked:enabled').each(function () {
            formData.append('ExamenResueltoForm[respuestasMul][]', $(this).val());
        });
        $('#seccion-REA input[name="ExamenResueltoForm[enunciadosCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[enunciadosCol][]', $(this).val());
        });
        $('#seccion-REA input[name="ExamenResueltoForm[respuestasCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[respuestasCol][]', $(this).val());
        });
        $('#seccion-REA input[name="ExamenResueltoForm[respuestasCom][]"]').each(function () {
            formData.append('ExamenResueltoForm[respuestasCom][]', $(this).val());
        });
        if (!formData.entries().next().value) {
            alert("Error at sending data");
            return;
        }
        $.ajax({
            url: "save-answers",
            type: "post",
            contentType: false,
            processData: false,
            data: formData,
            beforeSend: function(){
                $('#next').addClass('oculto');
                $('#spinner-examen').removeClass('oculto');
            },
            success: function (data) {
                if (data) {
                    var siguiente_seccion = $('.seccion.visible').data('siguiente');
                    if(siguiente_seccion != 'FIN'){
                        $('.seccion.visible').removeClass('visible').addClass('oculto');
                        $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                        var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                        iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                        changeWarningTypeText(bloque_visible);
                        $(window).scrollTop(0);
                    }else{
                        $('#next').text('End Test');
                    }
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                }else{
                    $.magnificPopup.open({
                        items: {
                            src: '#error-dialog',
                        },
                        modal: true,
                        type: 'inline'
                    });
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                    $('.retry-answers').on('click', function(){
                        var magnificPopup = $.magnificPopup.instance;
                        magnificPopup.close();
                        $('#next').trigger('click');
                    });
                }
            },
            error: function () {
                $('.error-web').text('Connection error: '+xhr.status);
                $.magnificPopup.open({
                    items: {
                        src: '#error-dialog',
                    },
                    modal: true,
                    type: 'inline'
                });
                $('#next').removeClass('oculto');
                $('#spinner-examen').addClass('oculto');
                $('.retry-answers').on('click', function(){
                    var magnificPopup = $.magnificPopup.instance;
                    magnificPopup.close();
                    $('#next').trigger('click');
                });
            }
        });
    } else {
        formData.append('ExamenResueltoForm[id]', $('input[name="ExamenResueltoForm[id]"]').val());
        $('#seccion-REA-'+ sectionNumber.toString() +' input[name="ExamenResueltoForm[preguntas][]"]').each(function () {
            formData.append('ExamenResueltoForm[preguntas][]', $(this).val());
        });
        $('#seccion-REA-'+ sectionNumber.toString() +' .preguntas-mult input:radio:checked:enabled').each(function () {
            formData.append('ExamenResueltoForm[respuestasMul][]', $(this).val());
        });
        $('#seccion-REA-'+ sectionNumber.toString() +' input[name="ExamenResueltoForm[enunciadosCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[enunciadosCol][]', $(this).val());
        });
        $('#seccion-REA-'+ sectionNumber.toString() +' input[name="ExamenResueltoForm[respuestasCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[respuestasCol][]', $(this).val());
        });
        if (!formData.entries().next().value) {
            alert("Error at sending data");
            return;
        }
        $.ajax({
            url: "save-answers",
            type: "post",
            contentType: false,
            processData: false,
            data: formData,
            beforeSend: function(){
                $('#next').addClass('oculto');
                $('#spinner-examen').removeClass('oculto');
            },
            success: function (data) {
                if(data){
                    var siguiente_seccion = $('.seccion.visible').data('siguiente');
                    if(siguiente_seccion != 'FIN'){
                        $('.seccion.visible').removeClass('visible').addClass('oculto');
                        $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                        var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                        iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                        changeWarningTypeText(bloque_visible);
                        $(window).scrollTop(0);
                    }else{
                        $('#next').text('End Test');
                    }
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                }else{
                    $.magnificPopup.open({
                        items: {
                            src: '#error-dialog',
                        },
                        modal: true,
                        type: 'inline'
                    });
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                    $('.retry-answers').on('click', function(){
                        var magnificPopup = $.magnificPopup.instance;
                        magnificPopup.close();
                        $('#next').trigger('click');
                    });
                }
            },
            error: function () {
                $('.error-web').text('Connection error: '+xhr.status);
                $.magnificPopup.open({
                    items: {
                        src: '#error-dialog',
                    },
                    modal: true,
                    type: 'inline'
                });
                $('#next').removeClass('oculto');
                $('#spinner-examen').addClass('oculto');
                $('.retry-answers').on('click', function(){
                    var magnificPopup = $.magnificPopup.instance;
                    magnificPopup.close();
                    $('#next').trigger('click');
                });
            }
        });
    }
}

function saveLisQuestions(sectionNumber = 0){
    var formData = new FormData();
    if(sectionNumber == 0){
        formData.append('ExamenResueltoForm[id]', $('input[name="ExamenResueltoForm[id]"]').val());
        $('#seccion-LIS input[name="ExamenResueltoForm[preguntas][]"]').each(function () {
            formData.append('ExamenResueltoForm[preguntas][]', $(this).val());
        });
        $('#seccion-LIS .preguntas-mult input:radio:checked:enabled').each(function () {
            formData.append('ExamenResueltoForm[respuestasMul][]', $(this).val());
        });
        $('#seccion-LIS input[name="ExamenResueltoForm[enunciadosCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[enunciadosCol][]', $(this).val());
        });
        $('#seccion-LIS input[name="ExamenResueltoForm[respuestasCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[respuestasCol][]', $(this).val());
        });
        $('#seccion-LIS input[name="ExamenResueltoForm[respuestasCom][]"]').each(function () {
            formData.append('ExamenResueltoForm[respuestasCom][]', $(this).val());
        });
        if (!formData.entries().next().value) {
            alert("Error at sending data");
            return;
        }
        $.ajax({
            url: "save-answers",
            type: "post",
            contentType: false,
            processData: false,
            data: formData,
            beforeSend: function(){
                $('#next').addClass('oculto');
                $('#spinner-examen').removeClass('oculto');
            },
            success: function (data) {
                if(data){
                    var siguiente_seccion = $('.seccion.visible').data('siguiente');
                    if(siguiente_seccion != 'FIN'){
                        $('.seccion.visible').removeClass('visible').addClass('oculto');
                        $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                        var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                        iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                        changeWarningTypeText(bloque_visible);
                        $(window).scrollTop(0);
                    }else{
                        $('#next').text('End Test');
                    }
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                }else{
                    $.magnificPopup.open({
                        items: {
                            src: '#error-dialog',
                        },
                        modal: true,
                        type: 'inline'
                    });
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                    $('.retry-answers').on('click', function(){
                        var magnificPopup = $.magnificPopup.instance;
                        magnificPopup.close();
                        $('#next').trigger('click');
                    });
                }
            },
            error: function () {
                $('.error-web').text('Connection error: '+xhr.status);
                $.magnificPopup.open({
                    items: {
                        src: '#error-dialog',
                    },
                    modal: true,
                    type: 'inline'
                });
                $('#next').removeClass('oculto');
                $('#spinner-examen').addClass('oculto');
                $('.retry-answers').on('click', function(){
                    var magnificPopup = $.magnificPopup.instance;
                    magnificPopup.close();
                    $('#next').trigger('click');
                });
            }
        });
    } else{
        formData.append('ExamenResueltoForm[id]', $('input[name="ExamenResueltoForm[id]"]').val());
        $('#seccion-LIS-'+ sectionNumber.toString() +' input[name="ExamenResueltoForm[preguntas][]"]').each(function () {
            formData.append('ExamenResueltoForm[preguntas][]', $(this).val());
        });
        $('#seccion-LIS-'+ sectionNumber.toString() +' .preguntas-mult input:radio:checked:enabled').each(function () {
            formData.append('ExamenResueltoForm[respuestasMul][]', $(this).val());
        });
        $('#seccion-LIS-'+ sectionNumber.toString() +' input[name="ExamenResueltoForm[enunciadosCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[enunciadosCol][]', $(this).val());
        });
        $('#seccion-LIS-'+ sectionNumber.toString() +' input[name="ExamenResueltoForm[respuestasCol][]"]').each(function () {
            formData.append('ExamenResueltoForm[respuestasCol][]', $(this).val());
        });
        if (!formData.entries().next().value) {
            alert("Error at sending data");
            return;
        }
        $.ajax({
            url: "save-answers",
            type: "post",
            contentType: false,
            processData: false,
            data: formData,
            beforeSend: function(){
                $('#next').addClass('oculto');
                $('#spinner-examen').removeClass('oculto');
            },
            success: function (data) {
                if(data){
                    var siguiente_seccion = $('.seccion.visible').data('siguiente');
                    if(siguiente_seccion != 'FIN'){
                        $('.seccion.visible').removeClass('visible').addClass('oculto');
                        $('#seccion-'+siguiente_seccion).removeClass('oculto').addClass('visible');
                        var bloque_visible = $('.seccion.visible').attr('id').split('-')[1];
                        iniciaContador((parseInt($('#tiempo-' + bloque_visible).val())), document.querySelector('#time'), (parseInt($('#tiempo-usado-' + bloque_visible).val())));
                        changeWarningTypeText(bloque_visible);
                        $(window).scrollTop(0);
                    }else{
                        $('#next').text('End Test');
                    }
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                }else{
                    $.magnificPopup.open({
                        items: {
                            src: '#error-dialog',
                        },
                        modal: true,
                        type: 'inline'
                    });
                    $('#next').removeClass('oculto');
                    $('#spinner-examen').addClass('oculto');
                    $('.retry-answers').on('click', function(){
                        var magnificPopup = $.magnificPopup.instance;
                        magnificPopup.close();
                        $('#next').trigger('click');
                    });
                }
            },
            error: function () {
                $('.error-web').text('Connection error: '+xhr.status);
                $.magnificPopup.open({
                    items: {
                        src: '#error-dialog',
                    },
                    modal: true,
                    type: 'inline'
                });
                $('#next').removeClass('oculto');
                $('#spinner-examen').addClass('oculto');
                $('.retry-answers').on('click', function(){
                    var magnificPopup = $.magnificPopup.instance;
                    magnificPopup.close();
                    $('#next').trigger('click');
                });
            }
        });
    }
}

function savePointsWriting(){
    var formData = $('#save-writing-points-form').serialize()
    $.ajax({
        url: "grade-exam",
        type: "post",
        data: formData,
        success: function (data) {
            if (data) {
                window.location.href = "next-exam"
            }
            else {
                return
            }
        },
        error: function () {
            // $('.loader').css("display","none");
            //alert("Something went wrong");
        }
    });
}

function saveWritingPartial(){
    var dict = {}
    var alumnoExamen = $('input[name="WritingResueltoForm[id]"]').val()
    var reactivo = $('input[name="WritingResueltoForm[reactivo]"]').val()
    var texto = $('textarea[name="WritingResueltoForm[texto]"]').val()
    if(!texto){
        texto = ''
    }
    dict.alumnoExamen = alumnoExamen
    dict.reactivo = reactivo
    dict.textoWriting = texto
    $.ajax({
        url: "save-writing-partial",
        type: "post",
        data: dict,
        success: function (data) {
            if (data) {
                return
            }
            else {
                return
            }
        },
        error: function () {
        }
    })
}

function saveUsedTime(){
    if($('#time-dialog').is(':visible') || $('#error-dialog').is(':visible')){
        return true;
    }
    var dict = {}
    var bloque_visible = $('.seccion.visible').attr('id').split('-')[1]
    var alumnoExamen = $('input[name="ExamenResueltoForm[id]"]').val()
    dict.seccion = bloque_visible
    dict.alumnoExamen = alumnoExamen
    $.ajax({
        url: "save-used-time",
        type: "post",
        data: dict,
        success: function (data) {
            if (data) {
                return
            }
            else {
                return
            }
        },
        error: function () {
        }
    })
}

function changeLastCharFromString(string, newValue) {
    var numChars = string.length;
    var firstString = string.substr(0, numChars - 1);
    return firstString + newValue;
}

function changeWarningTypeText(showedSection) {
    if (showedSection == "USE") {
        $('#warning-type').text('question');
    } else {
        $('#warning-type').text('question');
    }
}

function enableTooltips() {
    if ($('[data-tooltip="true"]').length > 0) {
        $('[data-tooltip="true"]').tooltip();
    }
}

$(document).ready(function(){


    $('#student-1').find('.change-student').click(function(){
        if (confirm("Si cambia de estudiante los campos llenados serán borrados, ¿Esta seguro de cambiar de estudiante?")) {
            $('#select-student-speaking-1').attr('disabled', false);
            var id = $('#scorespeakingform-0-student_id').val();
            $.ajax({
                url: "delete-section-speaking",
                type: "post",
                data: {id:id},
                success: function () {
                    var questions = $('#student-1').find('.table.visible').find('#speak-section-1').data('count') + $('#student-1').find('.table.visible').find('#speak-section-2').data('count') + $('#student-1').find('.table.visible').find('#speak-section-3').data('count');

                    for(var j = 0; j < questions; j++){
                        $('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+j+"]']").prop('checked', false)
                    }
                },
                error: function () {
                    alert("Something went wrong");
                }
            });
        }
    })
    $('#student-2').find('.change-student').click(function(){
        if (confirm("Si cambia de estudiante los campos llenados serán borrados, ¿Esta seguro de cambiar de estudiante?")) {
            $('#select-student-speaking-2').attr('disabled', false);
            var id = $('#scorespeakingform-1-student_id').val();
            $.ajax({
                url: "delete-section-speaking",
                type: "post",
                data: {id:id},
                success: function () {
                    var questions = $('#student-2').find('.table.visible').find('#speak-section-1').data('count') + $('#student-2').find('.table.visible').find('#speak-section-2').data('count') + $('#student-2').find('.table.visible').find('#speak-section-3').data('count');

                    for(var j = 0; j < questions; j++){
                        $('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+j+"]']").prop('checked', false)
                    }
                },
                error: function () {
                    alert("Something went wrong");
                }
            });
        }
    })
//    maneja el guardado de speaking v2 por secciones primer alumno seleccionado
    $('#select-student-speaking-1').change(function(){
//        select-student-speaking-1
        $('#select-student-speaking-1').attr('disabled', true);
        $('#student-1').find('.change-student').removeClass('btn-dis');
        $('#student-1').find('.change-student').removeAttr('disabled');

        var id;
        var id = $('#scorespeakingform-0-student_id').val();

        $.ajax({
            url: "get-section-speaking",
            type: "post",
            data: {id:id},
//                    dataType: 'json',
            success: function (resp) {
                if (resp){
                    resp = JSON.parse(resp);
                    var questions = $('#student-1').find('.table.visible').find('#speak-section-1').data('count') + $('#student-1').find('.table.visible').find('#speak-section-2').data('count') + $('#student-1').find('.table.visible').find('#speak-section-3').data('count');

                    for(var j = 0; j < questions; j++){
                        if (resp[j] != 0){
                            $('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+j+"]'][value="+resp[j]+"]").prop('checked', true)
                        }
                    }
                }
            },
            error: function () {
                alert("Something went wrong");
            }
        });

        var questions_section1;
        var questions_section2;
        var questions_section3;
        var questions_section1 = $('#student-1').find('.table.visible').find('#speak-section-1').data('count');
        var questions_section2 = $('#student-1').find('.table.visible').find('#speak-section-2').data('count');
        var questions_section3 = $('#student-1').find('.table.visible').find('#speak-section-3').data('count');
        var data = {};
        var answers = {};
        data.long1 = questions_section1;
        data.long2 = questions_section2;
        data.long3 = questions_section3;
        data.id = id;
//        maneja la seccion 1 del examen speaking v2
        for(var i = 0; i < questions_section1; i++){
            $('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+i+"]']").click(function(){
//                if ()

                for(var j = 0; j < questions_section1; j++){
                    if ($('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+j+"]']").is(':checked')){
                        answers[j] = $('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+j+"]']:checked").val();
                    } else {
                        return;
                    }
                }
//                for(var k = 0; k < questions_section1; k++){
//                }
                data.section = 1;
                data.answers = answers;
                if (id == $('#scorespeakingform-0-student_id').val()){
                    $.ajax({
                        url: "section-speaking",
                        type: "post",
                        data: data,
    //                    dataType: 'json',
                        success: function () {
                        },
                        error: function () {
                            alert("Something went wrong");
                        }
                    });
                }
            });
        };

//        maneja la seccion 2 del examen speaking v2
        for(var i = questions_section1; i < questions_section1+questions_section2; i++){
            $("input[name='ScoreSpeakingForm[0][scores]["+i+"]']").click(function(){
                for(var j = questions_section1; j < questions_section1+questions_section2; j++){
                    if ($('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+j+"]']").is(':checked')){
                    } else {
                        return;
                    }
                }
                var answers = {};
                for(var k = questions_section1; k < questions_section1+questions_section2; k++){
                    answers[k] = $('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+k+"]']:checked").val();
                }
                data.section = 2;
                data.answers = answers;
                if (id == $('#scorespeakingform-0-student_id').val()){
                    $.ajax({
                        url: "section-speaking",
                        type: "post",
                        data: data,
    //                    dataType: 'json',
                        success: function () {
                        },
                        error: function () {
                            alert("Something went wrong");
                        }
                    });
                }

            });
        };

//        maneja la seccion 3 del examen speaking v2
        for(var i = questions_section1+questions_section2; i < questions_section1+questions_section2+questions_section3; i++){
            $("input[name='ScoreSpeakingForm[0][scores]["+i+"]']").click(function(){
                for(var j = questions_section1+questions_section2; j < questions_section1+questions_section2+questions_section3; j++){
                    if ($('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+j+"]']").is(':checked')){
                    } else {
                        return;
                    }
                }
                var answers = {};
                for(var k = questions_section1+questions_section2; k < questions_section1+questions_section2+questions_section3; k++){
                    answers[k] = $('#student-1').find('.table.visible').find("input[name='ScoreSpeakingForm[0][scores]["+k+"]']:checked").val();
                }
                data.section = 3;
                data.answers = answers;
                if (id == $('#scorespeakingform-0-student_id').val()){
                    $.ajax({
                        url: "section-speaking",
                        type: "post",
                        data: data,
    //                    dataType: 'json',
                        success: function () {
                        },
                        error: function () {
                            alert("Something went wrong");
                        }
                    });
                }
            });
        };
    });

//    maneja el guardado de speaking v2 por secciones del segundo alumno seleccionado
    $('#select-student-speaking-2').change(function(){
        var id;
        var id = $('#scorespeakingform-1-student_id').val();

        $.ajax({
            url: "get-section-speaking",
            type: "post",
            data: {id:id},
//                    dataType: 'json',
            success: function (resp) {
                if (resp){
                    resp = JSON.parse(resp);
                    var questions = $('#student-2').find('.table.visible').find('#speak-section-1').data('count') + $('#student-2').find('.table.visible').find('#speak-section-2').data('count') + $('#student-2').find('.table.visible').find('#speak-section-3').data('count');

                    for(var j = 0; j < questions; j++){
                        $('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+j+"]'][value="+resp[j]+"]").prop('checked', true)
                    }
                }
            },
            error: function () {
                alert("Something went wrong");
            }
        });

        $('#select-student-speaking-2').attr('disabled', true);
        $('#student-2').find('.change-student').removeClass('btn-dis');
        $('#student-2').find('.change-student').removeAttr('disabled');


        var questions_section1;
        var questions_section2;
        var questions_section3;
        var questions_section1 = $('#student-2').find('.table.visible').find('#speak-section-1').data('count');
        var questions_section2 = $('#student-2').find('.table.visible').find('#speak-section-2').data('count');
        var questions_section3 = $('#student-2').find('.table.visible').find('#speak-section-3').data('count');
        var data = {};
        var answers = {};
        data.long1 = questions_section1;
        data.long2 = questions_section2;
        data.long3 = questions_section3;
        data.id = id;
//        maneja la seccion 1 del examen speaking v2
        for(var i = 0; i < questions_section1; i++){
            $("input[name='ScoreSpeakingForm[1][scores]["+i+"]']").click(function(){
                for(var j = 0; j < questions_section1; j++){
                    if ($('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+j+"]']").is(':checked')){
                    } else {
                        return;
                    }
                }
                var answers = {};
                for(var k = 0; k < questions_section1; k++){
                    answers[k] = $('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+k+"]']:checked").val();
                }
                data.section = 1;
                data.answers = answers;
                if (id == $('#scorespeakingform-1-student_id').val()){
                    $.ajax({
                        url: "section-speaking",
                        type: "post",
                        data: data,
    //                    dataType: 'json',
                        success: function () {
                        },
                        error: function () {
                            alert("Something went wrong");
                        }
                    });
                };
            });
        };

//        maneja la seccion 2 del examen speaking v2
        for(var i = questions_section1; i < questions_section1+questions_section2; i++){
            $("input[name='ScoreSpeakingForm[1][scores]["+i+"]']").click(function(){
                for(var j = questions_section1; j < questions_section1+questions_section2; j++){
                    if ($('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+j+"]']").is(':checked')){
                    } else {
                        return;
                    }
                }
                var answers = {};
                for(var k = questions_section1; k < questions_section1+questions_section2; k++){
                    answers[k] = $('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+k+"]']:checked").val();
                }
                data.section = 2;
                data.answers = answers;
                if (id == $('#scorespeakingform-1-student_id').val()){
                    $.ajax({
                        url: "section-speaking",
                        type: "post",
                        data: data,
    //                    dataType: 'json',
                        success: function () {
                        },
                        error: function () {
                            alert("Something went wrong");
                        }
                    });
                };
            });
        };

//        maneja la seccion 3 del examen speaking v2
        for(var i = questions_section1+questions_section2; i < questions_section1+questions_section2+questions_section3; i++){
            $("input[name='ScoreSpeakingForm[1][scores]["+i+"]']").click(function(){
                for(var j = questions_section1+questions_section2; j < questions_section1+questions_section2+questions_section3; j++){
                    if ($('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+j+"]']").is(':checked')){
                    } else {
                        return;
                    }
                }
                var answers = {};
                for(var k = questions_section1+questions_section2; k < questions_section1+questions_section2+questions_section3; k++){
                    answers[k] = $('#student-2').find('.table.visible').find("input[name='ScoreSpeakingForm[1][scores]["+k+"]']:checked").val();
                }
                data.section = 3;
                data.answers = answers;
                if (id == $('#scorespeakingform-1-student_id').val()){
                    $.ajax({
                        url: "section-speaking",
                        type: "post",
                        data: data,
    //                    dataType: 'json',
                        success: function () {
                        },
                        error: function () {
                            alert("Something went wrong");
                        }
                    });
                };
            });
        };
    });


});
