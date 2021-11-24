export const useLoading = (controller) =>
{
    const loadingFragment = document.createElement('div')

    loadingFragment.classList.add('animate-swinging','opacity-60','absolute','top-0','left-0','rounded','w-full','h-full','bg-gradient-to-r','from-lotgd-gray-50','via-lotgd-red-200','to-lotgd-gray-50')

    const hasRelative = controller.activatorTarget.classList.contains('relative')
    const hasOverflowHidden = controller.activatorTarget.classList.contains('overflow-hidden')

    function startLoading ()
    {
        //-- Disable button
        controller.activatorTarget.disabled = true

        if ( ! hasRelative) controller.activatorTarget.classList.add('relative')
        if ( ! hasOverflowHidden) controller.activatorTarget.classList.add('overflow-hidden')

        //-- Add loading
        controller.activatorTarget.appendChild(loadingFragment)
    }

    function stopLoading ()
    {
        if ( ! hasRelative) controller.activatorTarget.classList.remove('relative')
        if ( ! hasOverflowHidden) controller.activatorTarget.classList.remove('overflow-hidden')

        //-- Enable button
        controller.activatorTarget.disabled = false

        //-- Remove loading
        controller.activatorTarget.removeChild(loadingFragment)
    }

    Object.assign(controller, { startLoading, stopLoading })
}

export const useLoadingBarTop = (controller) =>
{
    const loadingFragment = document.createElement('div')

    loadingFragment.classList.add('fixed', 'inset-x-0', 'top-0', 'p-1', 'pointer-events-none', 'animate-swinging', 'bg-gradient-to-r','from-lotgd-500','via-lotgd-800','to-lotgd-500')
    loadingFragment.style.zIndex = 9999

    function startLoadingBarTop ()
    {
        //-- Add loading
        document.getElementsByTagName('body')[0].appendChild(loadingFragment)
    }

    function stopLoadingBarTop ()
    {
        //-- Remove loading
        document.getElementsByTagName('body')[0].removeChild(loadingFragment)
    }

    Object.assign(controller, { startLoadingBarTop, stopLoadingBarTop })
}
