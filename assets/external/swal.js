/**
 * Apply a common configuration for Swal modal
 */
define([
    './jquery',
    'sweetalert2'
], function (jQuery, swal)
{
    'use strict'

    var modal = {
        //-- Full default configuration
        defaultParams: {
            title: '',
            titleText: '',
            text: '',
            html: '',
            type: null,
            customClass: '',
            target: 'body',
            animation: true,
            allowOutsideClick: true,
            allowEscapeKey: true,
            allowEnterKey: true,
            showConfirmButton: true,
            showCancelButton: false,
            preConfirm: null,
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            confirmButtonClass: 'ui green button',
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#aaa',
            cancelButtonClass: 'ui red button',
            buttonsStyling: false, //-- Not need, has a custom configuration
            reverseButtons: false,
            focusCancel: false,
            showCloseButton: false,
            showLoaderOnConfirm: false,
            imageUrl: null,
            imageWidth: null,
            imageHeight: null,
            imageClass: null,
            timer: null,
            width: 500,
            padding: 20,
            background: '#fff',
            input: null,
            inputPlaceholder: '',
            inputValue: '',
            inputOptions: {},
            inputAutoTrim: true,
            inputClass: null,
            inputAttributes: {},
            inputValidator: null,
            progressSteps: [],
            currentProgressStep: null,
            progressStepsDistance: '40px',
            onOpen: null,
            onClose: null
        },
        swalCustom: {},

        //-- Change configuration of swal
        configChange (options)
        {
            options = options || {}

            this.swalCustom = swal.mixin(jQuery.extend({}, this.defaultParams, options))
        },

        //-- Restart default custom configuration
        configRestart ()
        {
            this.configChange()
        },

        //-- Get instance
        get ()
        {
            return this.swalCustom
        },

        //-- Init swall with custom configuration
        init ()
        {
            if (this.initiated === false)
            {
                this.configChange()
                this.initiated = true
            }
        }
    }

    modal.init()

    return modal
})
