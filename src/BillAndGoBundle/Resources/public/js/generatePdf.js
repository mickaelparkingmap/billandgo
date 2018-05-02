/**
 * Created by kevinhuron on 18/07/2017.
 * Generate PDF in JS - Here Whe generate the Bill and the Quote and MORE
 */
$(document).ready(function () {

    var gen = false;
    $("#generatePDF").unbind('click').bind('click');
    $("#generatePDF").click(function (e) {
        e.preventDefault();
        if (gen == true)
            return (false);
        gen = true;
        var typePdf = $("#doc_type").text();
        var ref_inter = $("#doc_number").text();
        var user_firstname = $("#user .firstname").text();
        var user_lastname = $("#user .lastname").text();
        var user_companyname = $("#user .companyname").text();
        var user_adress = $("#user .adress").text();
        var user_city = $("#user .city").text();
        var user_country = $("#user .country").text();
        var user_zip = $("#user .zipcode").text();
        var user_phone = $("#user .phone").text();
        var client_companyname = $("#client .companyname").text();
        var client_adress = $("#client .adress").text();
        var client_city = $("#client .city").text();
        var client_country = $("#client .country").text();
        var client_zip = $("#client .zipcode").text();
        var date = $("#date").text();

        var numfac = $(this).attr("attr-num");
        var companyfac = $(this).attr("attr-companyname");


        var lines = [
            [
                {
                    text: 'Description',
                    style: 'tableHeader',
                    fillColor: '#262626',
                    color: 'white',
                    bold: false,
                    fontSize: 10,
                    margin: [6, 6]
                },
                {
                    text: 'Quantité',
                    style: 'tableHeader',
                    fillColor: '#262626',
                    color: 'white',
                    bold: false,
                    fontSize: 10,
                    margin: [6, 6],
                    alignment: 'right'
                },
                {
                    text: 'Prix unitaire',
                    style: 'tableHeader',
                    fillColor: '#262626',
                    color: 'white',
                    bold: false,
                    fontSize: 10,
                    margin: [6, 6],
                    alignment: 'right'
                },
                {
                    text: 'Prix total',
                    style: 'tableHeader',
                    fillColor: '#262626',
                    color: 'white',
                    bold: false,
                    fontSize: 10,
                    margin: [6, 6],
                    alignment: 'right'
                }
            ]
        ];
        $(".line-elt" ).each(function() {
            var elt = [
                {text: $(this).find(".line-elt-name").text() + '\n' + $(this).find(".description").text(), italics: false, bold: false, fontSize: 10, color: 'gray', margin: [5, 2]},
                {text: $(this).find(".quantity").text(), italics: false, bold: false, fontSize: 10, margin: [2, 2], alignment: 'right'},
                {text: $(this).find(".uht").text() + '€  HT \n' + $(this).find(".utc").text() + '€ TTC', italics: false, bold: false, fontSize: 8, margin: [2, 2], alignment: 'right'},
                {text: $(this).find(".tht").text() + '€  HT \n' + $(this).find(".ttc").text() + '€ TTC', italics: false, bold: false, fontSize: 8, margin: [2, 2], alignment: 'right'}
            ];
            lines.push(elt);
        });
        lines.push(
            [
                '',
                {text: 'Total', italics: false, bold: true, fontSize: 10, fillColor: '#dddddd', margin: [6, 6]
                },
                {text: '', fillColor: '#dddddd'},
                {text: $("#totalHT").text()+'  HT \n'+$("#totalTTC").text()+' TTC', italics: false, bold: true, fontSize: 10, fillColor: '#dddddd', margin: [6, 6], alignment: 'right'}
            ]
        );



        getImageDataURL(imgDoc, function (rs) {
            var imgNew = rs['data'];
            var docDefinition = {
                content: [
                    {
                        columns: [
                            {
                                image: imgNew,
                                width: 200
                            },
                            {
                                text: [
                                    {text: typePdf, fontSize: 35, alignment: 'right', bold: false},
                                    {text: '\nn° ', fontSize: 10, alignment: 'right', bold: false, color: 'grey'},
                                    {
                                        text: ref_inter + ' \n',
                                        fontSize: 11,
                                        alignment: 'right',
                                        bold: true,
                                        color: 'grey'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        columns: [
                            {
                                text: [
                                    {text: user_companyname + '\n', fontSize: 13, bold: true},
                                    {
                                        text: user_adress + '\n'
                                        + user_zip + ' ' + user_city + ' ' + user_country + '\n'
                                        + user_phone + '\n\n',
                                        fontSize: 10, bold: false, color: 'grey'
                                    }
                                ]
                            },
                            {
                                text: [
                                    {text: 'Destinataire :\n ', fontSize: 10, bold: false, color: 'grey', alignment: 'right'},
                                    {
                                        text: client_companyname + '\n'
                                        + user_adress + '\n' + user_zip + ' ' + user_city + ' ' + user_country,
                                        fontSize: 10, bold: false, alignment: 'right'
                                    }
                                ]
                            }
                        ]
                    },
                    {columns: [{text: '\n'}]},
                    {
                        columns: [
                            {text: ''},
                            {text: ''},
                            {
                                text: [
                                    {
                                        text: '\n Date :\t', alignment: 'left',
                                        fontSize: 10, bold: false, color: 'grey'
                                    },
                                    {
                                        text: ' \t '+ date + ' \n', alignment: 'right',
                                        fontSize: 10, bold: false
                                    }
                                ]
                            }
                        ]
                    },
                    {columns: [{text: '\n'}]},
                    {
                        table: {
                            headerRows: 1,
                            widths: [250, '*', '*', '*'],
                            body: lines
                        },
                        layout: 'lightHorizontalLines'
                    },
                    {columns: [{text: '\n'}]},
                    {columns: [{text: '\n'}]},
                    {columns: [{text: '\n'}]},
                    {
                        columns: [
                            {
                                text: [
                                    {text: 'Remarque \n', italics: false, bold: false, fontSize: 13, color: 'grey'},
                                    {text: 'TVA non applicable, art. 293 B du CG', italics: false, bold: false, fontSize: 10}
                                ]
                            }
                        ]
                    }
                ]/*,
            footer: {
                columns: [
                    {
                        text: [
                            {
                                text: 'Aznotis - 815 242 235 - 6 Allée Andrea 93140 BONDY', italics: false, bold: true,
                                fontSize: 6, alignment: 'center'
                            }
                        ]
                    }
                ]
            }*/
            };
            // open the PDF in a new window
            pdfMake.createPdf(docDefinition).download(numfac+'_'+companyfac);
            gen = false;
            return (false);
        });



    });
});

/**
 * Converts image URLs to dataURL schema using Javascript only.
 *
 * @param {String} url Location of the image file
 * @param {Function} success Callback function that will handle successful responses. This function should take one parameter
 *                            <code>dataURL</code> which will be a type of <code>String</code>.
 * @param {Function} error Error handler.
 *
 * @example
 * var onSuccess = function(e){
 * 	document.body.appendChild(e.image);
 * 	alert(e.data);
 * };
 *
 * var onError = function(e){
 * 	alert(e.message);
 * };
 *
 * getImageDataURL('myimage.png', onSuccess, onError);
 *
 */
function getImageDataURL(url, success, error) {
    var data, canvas, ctx;
    var img = new Image();
    img.onload = function(){
        // Create the canvas element.
        canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        // Get '2d' context and draw the image.
        ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        // Get canvas data URL
        try{
            data = canvas.toDataURL();
            success({image:img, data:data});
        }catch(e){
            error(e);
        }
    }
    // Load image URL.
    try{
        img.src = url;
    }catch(e){
        error(e);
    }
}