async function createPopup(url: string) {
    const popupWindow = window.open(url, '_blank', 'height=' + window.screen.height + ', width=' +  window.screen.width);
    if (!popupWindow)
        return;

    popupWindow.document.title = 'External Popup Title';
    popupWindow.focus();

    // Close the popup with tab
    window.addEventListener('unload', async function() { 
        popupWindow.close();
    }); 
}