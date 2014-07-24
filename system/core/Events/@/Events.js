/**
 * JavaScript Events library
 *
 * @/System/Events/Events.js
 *
 * Version 1.0
 *
 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */



function Events() {
    this._list = {};

    this.on = function(event, closure, once) {
        event.split(" ").map(function(value) {
            if (this._list[value] == undefined) {
                this._list[value] = [];
            }

            this._list[value].push({closure: closure, once: (once == undefined ? false : once)});
        }, this);
    };

    this.call = function(event, data) {
        if (typeof this._list[event] == "object") {
            this._list[event].map(function(value, index) {
                value.closure(data);

                if (value.once) {
                    delete this._list[event][index];
                }
            }, this);
        }
    };

    this.remove = function(event) {
        event.split(" ").map(function(value) {
            delete this._list[value];
        }, this);
    };
}