export const useLoading = (controller) =>
{
    let loading = false
    const loadingFragment = document.createElement('div')

    loadingFragment.classList.add('animate-swinging','opacity-60','absolute','top-0','left-0','rounded','w-full','h-full','bg-gradient-to-r','from-lotgd-gray-50','via-lotgd-red-200','to-lotgd-gray-50')

    let hasRelative = false
    let hasOverflowHidden = false

    if (controller.hasActivatorTarget)
    {
        hasRelative = controller.activatorTarget.classList.contains('relative')
        hasOverflowHidden = controller.activatorTarget.classList.contains('overflow-hidden')
    }

    function startLoading ()
    {
        //-- Disable button
        controller.activatorTarget.disabled = true

        if ( ! hasRelative) controller.activatorTarget.classList.add('relative')
        if ( ! hasOverflowHidden) controller.activatorTarget.classList.add('overflow-hidden')

        //-- Add loading
        controller.activatorTarget.appendChild(loadingFragment)
        loading = true
    }

    function stopLoading ()
    {
        if ( ! hasRelative) controller.activatorTarget.classList.remove('relative')
        if ( ! hasOverflowHidden) controller.activatorTarget.classList.remove('overflow-hidden')

        //-- Enable button
        controller.activatorTarget.disabled = false

        //-- Remove loading
        controller.activatorTarget.removeChild(loadingFragment)

        loading = false
    }

    Object.assign(controller, { startLoading, stopLoading, loading })
}

export const useLoadingBarTop = (controller) =>
{
    let loading = false
    const loadingFragment = document.createElement('div')

    loadingFragment.classList.add('fixed', 'inset-x-0', 'top-0', 'p-1', 'pointer-events-none', 'animate-swinging', 'bg-gradient-to-r','from-lotgd-500','via-lotgd-800','to-lotgd-500')
    loadingFragment.style.zIndex = 9999

    function startLoadingBarTop ()
    {
        //-- Add loading
        document.getElementsByTagName('body')[0].appendChild(loadingFragment)
        loading = true
    }

    function stopLoadingBarTop ()
    {
        //-- Remove loading
        document.getElementsByTagName('body')[0].removeChild(loadingFragment)
        loading = false
    }

    Object.assign(controller, { startLoadingBarTop, stopLoadingBarTop, loading })
}

export const useButtonLoading = (controller) =>
{
    let loading = false
    const loadingFragment = document.createElement('div')

    loadingFragment.classList.add('animate-swinging','opacity-60','absolute','top-0','left-0','rounded','w-full','h-full','bg-gradient-to-r','from-lotgd-gray-50','via-lotgd-red-200','to-lotgd-gray-50')

    let hasRelative = false
    let hasOverflowHidden = false

    function startButtonLoading (activator)
    {
        //-- Disable button
        activator.disabled = true

        hasRelative = activator.classList.contains('relative')
        hasOverflowHidden = activator.classList.contains('overflow-hidden')

        if ( ! hasRelative) activator.classList.add('relative')
        if ( ! hasOverflowHidden) activator.classList.add('overflow-hidden')

        //-- Add loading
        activator.appendChild(loadingFragment)

        loading = true
    }

    function stopButtonLoading (activator)
    {
        if ( ! hasRelative) activator.classList.remove('relative')
        if ( ! hasOverflowHidden) activator.classList.remove('overflow-hidden')

        //-- Enable button
        activator.disabled = false

        //-- Remove loading
        activator.removeChild(loadingFragment)

        loading = false
    }

    Object.assign(controller, { startButtonLoading, stopButtonLoading, loading })
}

/**
 * Show a Sync icon in top/bottom left/right
 * @param {object} controller
 */
export const useSyncIcon = (controller, y = 'top', x = 'right', color = '') =>
{
    let loading = false
    const span = document.createElement('span')
    const icon = document.createElement('i')

    span.classList.add('flex', 'absolute', 'w-auto', 'h-auto', `${y}-0`, `${x}-0`, `-m${y.charAt(0)}-1`, `-m${x.charAt(0)}-1`)
    icon.classList.add('animate-spin', 'absolute', 'inline-flex', 'fas', 'fa-sync', color)

    span.appendChild(icon)

    let hasRelative = false

    function startSyncIcon (activator)
    {
        //-- Disable button
        activator.disabled = true

        hasRelative = activator.classList.contains('relative')

        if ( ! hasRelative) activator.classList.add('relative')

        //-- Add loading
        activator.appendChild(span)

        loading = true
    }

    function stopSyncIcon (activator)
    {
        if ( ! hasRelative) activator.classList.remove('relative')

        //-- Enable button
        activator.disabled = false

        //-- Remove loading
        activator.removeChild(span)

        loading = false
    }

    Object.assign(controller, { startSyncIcon, stopSyncIcon, loading })
}
