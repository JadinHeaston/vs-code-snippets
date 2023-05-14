function setFocus(selection: string) {
    const focusElem = document.querySelector(selection) as HTMLElement | null;

    if (focusElem !== null)
        focusElem.focus();
}
