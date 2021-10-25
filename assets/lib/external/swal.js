/**
 * Apply a common configuration for Swal modal
 */
define([
    './jquery',
    'sweetalert2'
], function (jQuery, swal)
{
    'use strict'

    const modal = {
        //-- Full default configuration
        defaultParams: {
            title: '',
            titleText: '',
            text: '',
            html: '',
            icon: null,
            target: 'body',
            showClass: {
                popup: 'swal2-show',
                backdrop: 'swal2-backdrop-show',
                icon: 'swal2-icon-show'
            },
            hideClass: {
                popup: 'swal2-hide',
                backdrop: 'swal2-backdrop-hide',
                icon: 'swal2-icon-hide'
            },
            allowOutsideClick: true,
            allowEscapeKey: true,
            allowEnterKey: true,
            showConfirmButton: true,
            showCancelButton: false,
            preConfirm: null,
            customClass: {
                container: null,
                popup: null,
                header: null,
                title: null,
                closeButton: null,
                icon: null,
                image: null,
                content: null,
                input: null,
                actions: null,
                confirmButton: 'unstyle bg-lotgd-800 hover:bg-lotgd-500 px-3 py-2 rounded text-lotgd-gray-100 mr-1',
                cancelButton: 'unstyle bg-lotgd-red-800 hover:bg-lotgd-red-500 px-3 py-2 rounded text-lotgd-gray-100 mr-1',
                footer: null
            },
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#aaa',
            buttonsStyling: false, //-- Not need, has a custom configuration
            reverseButtons: false,
            focusCancel: false,
            showCloseButton: false,
            showLoaderOnConfirm: false,
            imageUrl: null,
            imageWidth: null,
            imageHeight: null,
            timer: null,
            width: 500,
            padding: 20,
            background: '#fff',
            input: null,
            inputPlaceholder: '',
            inputValue: '',
            inputOptions: {},
            inputAutoTrim: true,
            inputAttributes: {},
            inputValidator: null,
            progressSteps: [],
            currentProgressStep: null,
            progressStepsDistance: '40px',
            didOpen: null,
            willClose: null
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
