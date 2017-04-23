var Alexa = require('alexa-sdk');
var http = require('http');

const handlers = {

    'LaunchRequest': function () {
        this.emit('SayHi');
    },

    'GetNextBussesIntent': function () {
        this.emit('GetNextBusses');
    },

    'GetParkingIntent': function () {
        this.emit('GetParking');
    },

    'GetFoodIntent': function () {
        this.emit('GetFood');
    },

    'SayHi': function () {
        this.emit(':tell', 'Ich bin deine Uni. Frage mich nach dem Speiseplan, dem Busfahrplan oder freien Parkplätzen in Regensburg fragen.')
    },

    'GetNextBusses': function () {
        "use strict";
        let url = 'http://rvv.hosting9427.af958.netcup.net/index.php'

        http.get(url, (res) => {

            var body = ""

            let that = this

            res.on("data", function(chunk) {
                body += chunk
            })

            res.on("end", function() {
                var stops = JSON.parse(body);

                var message = ''

                for (let i = 0; i < stops.length; i++) {
                    message += 'Der Bus Richtung ' + stops[i].name + ' fährt in ' + stops[i].leavesIn + ' Minuten. '
                }

                if (message !== undefined) { 
                    that.emit(':tell', message)
                }
            })

        }).on('error', (socket) => {
            this.emit(':tell', 'Ups. Da ist wohl ein Fehler passiert.')
        })
    },

    'GetParking': function () {
        "use strict";
        let url = 'http://rvv.hosting9427.af958.netcup.net/parken.php'
        // console.log(this.event.request.intent.slots.Garage)
        var itemSlot = this.event.request.intent.slots.Garage;
        var itemName;
        if (itemSlot && itemSlot.value) {
            itemName = itemSlot.value.toLowerCase();
            http.get(url, (res) => {

                var body = ""

                let that = this

                res.on("data", function(chunk) {
                    body += chunk
                })

                res.on("end", function() {
                    var garages = JSON.parse(body);

                    for (let i = 0; i < garages.length; i++) {
                        let garage = garages[i].toLowerCase()
                        let re = new RegExp(itemName,"g");
                        if (String(garage).match(re) !== null) {
                            that.emit(':tell', garages[i])
                            break;
                        }
                        // message += garages[i] + '. '
                    }
                })
            }).on('error', (socket) => {
                this.emit(':tell', 'Ups. Da ist wohl ein Fehler passiert.')
            })
        } else {
            this.emit(':tell', 'Unbekannter Parkplatz.')
        }
    },

    'GetFood': function () {
        "use strict";

        let url = 'http://rvv.hosting9427.af958.netcup.net/mensa.php'
        let itemSlot = this.event.request.intent.slots.MeinDatum;

        if (itemSlot && itemSlot.value) {
            url = 'http://rvv.hosting9427.af958.netcup.net/mensa.php?datum=' + itemSlot.value;
        }

        http.get(url, (res) => {

            var body = ""
            let that = this

            res.on("data", function(chunk) {
                body += chunk
            })

            res.on("end", function() {
                var meals = JSON.parse(body);

                var message = 'Die Mensa hat folgendes Angebot:'

                for (let i = 0; i < meals.length; i++) {
                    message += ' ' + meals[i] + ','
                }

                if (message !== undefined) { 
                    that.emit(':tell', message)
                }
            })

        }).on('error', (socket) => {
            this.emit(':tell', 'Ups. Da ist wohl ein Fehler passiert.')
        })
    },


    'Unhandled': function () {
        this.emit(':tell', 'Da ist etwas schief gelaufen.');
    }

};

exports.handler = function(event, context, callback){
    var alexa = Alexa.handler(event, context);
    alexa.registerHandlers(handlers);
    alexa.execute();
};