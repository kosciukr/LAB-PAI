document.querySelectorAll('.aDodajDoKoszyka').forEach((elem) => {
    elem.addEventListener('click', async (e) => {
        e.preventDefault()
        const a = e.currentTarget
        const href = a.getAttribute('href')
        const resp = await fetch(href, {method: 'post'})
        const text = await resp.text()
        const liczba_ofert = $("#liczba_ofert");

        if (text === 'ok') {
            liczba_ofert.html(parseInt(liczba_ofert.text())+1)
            const ok = document.createElement('i')
            ok.classList.add('fas', 'fa-check-circle', 'text-success')
            a.parentNode.replaceChild(ok, a)
        } else {
            alert('Wystąpił nieoczekiwany błąd')
        }
    })
})

document.querySelectorAll('.aUsunZKoszyka').forEach((elem) => {
    elem.addEventListener('click', async (e) => {
        e.preventDefault()
        const a = e.currentTarget
        const href = a.getAttribute('href')
        const resp = await fetch(href, {method: 'post'})
        const text = await resp.text()
        const liczba_ofert = $("#liczba_ofert");

        if (text === 'ok') {
            liczba_ofert.html(parseInt(liczba_ofert.text())-1)
            row = a.parentNode.parentNode.rowIndex;
            document.getElementById("tbKoszyk").deleteRow(row);
        } else {
            alert('Wystąpił nieoczekiwany błąd')
        }
    })
})