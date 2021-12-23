import Swal from 'sweetalert2'
import merge from 'lodash.merge'
import './swal.css'

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
            confirmButton: 'w-auto mr-1',
            denyButton: 'w-auto input-red mr-1',
            cancelButton: 'w-auto input-red mr-1',
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

        this.swalCustom = Swal.mixin(merge({}, this.defaultParams, options))
    },

    //-- Restart default custom configuration
    configRestart ()
    {
        this.configChange()
    },

    //-- Init swall with custom configuration
    init ()
    {
        if (this.initiated === false)
        {
            this.configChange()
            this.initiated = true
        }
    },

    //-- Get instance
    get ()
    {
        return this.swalCustom
    }
}

modal.init()

export default modal
