

$( document ).ready(function() {

  let datos=[];


  $.ajax({
    type: "POST",
    async: false,
    url: "getData.php",
    dataType: "json",
   
    success: function(data) {
        console.log(data)
                         console.log(data.series);
                         console.log(data.plotlines);
                            plot(data);
                        // plot(data.series,data.plotlines);
                         //console.log(JSON.stringify(data.plotlines))
    }
});

 // plot(datos);
    function plot(data,plotlines) { 

       let newplotlines = [];

     

        Highcharts.setOptions({
            lang: {
                loading: 'Cargando...',
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                shortMonths: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                exportButtonTitle: "Exportar",
                printButtonTitle: "Importar",
                rangeSelectorFrom: "Desde",
                rangeSelectorTo: "Hasta",
                rangeSelectorZoom: "Periodo",
                contextButtonTitle: 'Exportar',
                downloadPNG: 'Descargar imagen PNG',
                downloadJPEG: 'Descargar imagen JPEG',
                downloadPDF: 'Descargar imagen PDF',
                downloadSVG: 'Descargar imagen SVG',
                downloadXLS: 'Descargar Archivo Excel',
                viewData: 'Ver Data',
                printChart: 'Imprimir',
                resetZoom: 'Reiniciar zoom',
                resetZoomTitle: 'Reiniciar zoom',
                decimalPoint: ",",
                thousandsSep: '.',
            },
            scrollbar: {
                barBackgroundColor: '#e6e6e6',
                barBorderRadius: 7,
                barBorderWidth: 0,
                buttonBackgroundColor: '#e6e6e6',
                buttonBorderWidth: 0,
                buttonBorderRadius: 7,
                trackBackgroundColor: 'none',
                trackBorderWidth: 1,
                trackBorderRadius: 8,
                trackBorderColor: '#CCC'
            },
            exporting: {
                enabled: false
            },
            rangeSelector: {
                enabled: true,
                inputEnabled: true,
                allButtonsEnabled: true,
                inputDateFormat: '%d-%m-%Y',
                buttons: [{
                    type: 'month',
                    count: 1,
                    text: '1m',
                    title: '1 mes'
                }, {
                    type: 'month',
                    count: 3,
                    text: '3m'
                }, {
                    type: 'month',
                    count: 6,
                    text: '6m'
                }, {
                    type: 'year',
                    count: 1,
                    text: '1 Año'
                }, {
                    type: 'all',
                    text: 'Todo'
                }],
                buttonTheme: {
                    fill: 'none',
                    stroke: 'none',
                    width: 60,
                    'stroke-width': 0,
                    r: 10,
                    style: {
                        fontWeight: 'bold',
                        fontSize: '12px'
                    },
                    states: {
                        hover: {},
                        select: {
                            fill: '#2ab4c042',
                            style: {
                                color: '#2ab4c0'
                            }
                        }
                    }
                }
            }
        });

        var chart = $('#chartdiv').highcharts({
            chart: {
                animation: {
                    duration: 1000,
                    easing: 'easeOutBounce',
                    style: {
                        font: '12px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                        color: 'gray',
                    }
                },
                plotBorderWidth: 1,
                zoomType: 'x',
                alignTicks: false,
            },
            title: {
                text: '',
                style: {
                    font: '13px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    color: 'black',
                }
            },
            scrollbar: {
                enabled: true
            },
            scrollbar: {
                enabled: true,
                barBackgroundColor: '#e6e6e6',
                barBorderRadius: 7,
                barBorderWidth: 0,
                buttonBackgroundColor: '#eee',
                buttonBorderWidth: 0,
                buttonBorderRadius: 7,
                trackBackgroundColor: 'none',
                trackBorderWidth: 1,
                trackBorderRadius: 8,
                trackBorderColor: '#CCC'
            },
            subtitle: {
                style: {
                    font: '11px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    color: 'gray',
                }
            },
            rangeSelector: {
                enabled: false
            },
            navigator: {
                enabled: true,
                outlineWidth: 1,
                handles: {
                    backgroundColor: '#eee',
                    borderColor: '#777'
                },
                series: {
                    type: 'line',
                    /*fillOpacity: 1,
                    lineWidth: 0*/
                },
                xAxis: {
                    gridLineWidth: 0,
                    labels: {
                        enabled: false
                    }
                },
               
            },
            exporting: {
                tableCaption: 'Data table',
                csv: {
                    dateFormat: '%Y-%m-%d'
                },
                xls: {
                    dateFormat: '%Y-%m-%d'
                },
                enabled: true,
                buttons: {
                    contextButton: {
                        menuItems: ['printChart', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS', ]
                    }
                },
                chartOptions: {
                    navigator: {
                        enabled: false
                    },
                    scrollbar: {
                        enabled: false
                    }
                }
            },
            legend: {
                enabled: true,
                itemStyle: {
               
                    font: '11px "Poppins"',
                    color: 'gray',
                }
            },
            xAxis: {
                //gridLineWidth: 1,
                minorTickInterval: 'auto',
                type: 'datetime',
                plotLines: data.plotlines,
               
                title: {
                    text: 'Fecha',
                    style: {
                        color: 'white'
                    }
                },
                style: {
                    color: '#edc240',
                     font: '11px "Poppins"',
                    
                    /*font: '11px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                     */
                },
                labels: {
                    style: {
                       /* font: '11px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    */
                         font: '11px "Poppins"',
                    }
                },
            },
            plotOptions: {
                series: {
                    pointWidth: 2,

                    allowPointSelect: true,
                    showInNavigator: true,
                    connectNulls: true,
                    marker: {
                        radius: 3,
                    }
                   
                },
                tooltip: {
                    shared: true,
                    headerFormat: '<span style="font-size: 10px">{point.x: %d-%m-%Y}</span><br/>',
                    pointFormat: '{series.name}' + ':<b>' + '{point.y}' + '</b>',
                    style: {
                        /*font: '11px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    */ font: '10px "Poppins"',
                    }
                },
               
             
              
            },
            credits: {
                text: '<b class="arial " style="color:royalblue">GP </b> <span class="times" style="color: black">Consultores Ltda<span>',
                href: 'https://www.gpconsultores.cl/',
                position: {
                    align: 'left',
                    verticalAlign: 'bottom',
                    x: 10,
                    y: -5,
                },
                style: {
                    "cursor": "pointer",
                    "color": "#2ab4c0",
                    "fontSize": "11px"
                }
            },
            credits: {
                enabled: false,
            },
            tooltip: {
                shared: true,
                xDateFormat: '%d-%m-%Y',
                style: {
                  font: '10px "Poppins"',
                 }
            },
            series: data.series,
            lang: {
                    noData: "No hay datos disponibles para Graficar"
            },
            noData: {
                style: {
                   
                     font: '13px "Poppins"',
                }
            },
            yAxis: [{ 
                title: {
                    text: ' ',
                    style: {
                        color: 'grey',
                         font: '11px "Poppins"',
                    },
                },
                labels: {
                    format: '{value:.2f}',
                    style:{
                         font: '11px "Poppins"',
                     }
                },
                opposite: false,
                margin: 15,
                gridLineWidth: 0, 
                
            }, {
               
                title: {
                    text: '',
                    style: {
                         font: '11px "Poppins"',
                        color: 'grey'
                    },
                },
                labels: {
                    format: '{value:.1f}',
                    style: {
                         font: '11px "Poppins"',
                    }
                },
                opposite: true,
                margin: 15,
                gridLineWidth: 0,
                minorTickInterval: 'auto',
                reversed: true
            }, {
                title: {
                    text: '',
                    style: {
                        color: 'grey',
                         font: '11px "Poppins"',
                    },
                    margin: 15
                },
                labels: {
                    format: '{value:.2f}',
                    style: {
                        /*font: '11px "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    */
                     font: '11px "Poppins"',
                    }
                },
                opposite: true,
            }],
        });
        
        var chart = $('#chartdiv').highcharts();  
        chart.xAxis[0].setExtremes((data.min), (chart.xAxis[0].dataMax));
        chart.yAxis[0].setExtremes((500), (6000));
        chart.yAxis[1].setExtremes((0), (500));
;


        
    }


});

