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
