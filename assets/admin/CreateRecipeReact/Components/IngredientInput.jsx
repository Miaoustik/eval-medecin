import React, {useCallback} from "react";

export default function ({filterIngredient, handleRemove, setInputs, handleBlur, handleFocus, suggestionActive, suggestions, index, handleChange, value, withBtn = true}){

    const suggestionOn = suggestionActive == value.id

    const handleMouseDown = useCallback((e) => {
        e.preventDefault()
    }, [])

    const handleClick = useCallback((e) => {
        e.preventDefault()
        const itemName = e.target.getAttribute('data-item')
        const inputId = e.target.getAttribute('data-id')
        setInputs((prevState) => {
            const newState = [...prevState]
            newState[inputId]['name'] = itemName
            return newState
        })
        filterIngredient(itemName)
    }, [])

    return (

        <div className={'d-flex mb-2 '}>
            <input data-index={index} data-inputname={'quantity'} onChange={handleChange} className={'form-control me-3'} type={'text'} value={value.quantity}/>
            <div className={"position-relative w-100"}>
                <input onBlur={handleBlur} onFocus={handleFocus} data-id={value.id} data-index={index} data-inputname={'name'} onChange={handleChange} className={'form-control'} type={'text'} value={value.name}/>
                {!((suggestions.length === 1) && (suggestions[0].name === value.name)) && value.name !== '' && suggestions.length > 0 && <div
                    className={'position-absolute w-100 bg-white rounded-bottom shadow1 mt-2' + (suggestionOn ? ' border border-primary' : '')}>
                    {suggestionOn && suggestions.map((e, k) => {
                        return (
                            <p role={'button'} onMouseDown={handleMouseDown} onClick={handleClick} data-item={e.name}
                               data-id={index}
                               className={'text-secondary textNoto pt-2 ps-4' + (k !== 0 ? ' border-top border-primary' : '')}
                               key={e.name}>{e.name}</p>
                        )
                    })}
                </div>}
            </div>

            {withBtn && <button className={'btn btn-primary text-white ms-2'} data-index={index} onClick={handleRemove}>X</button>}
        </div>
    )
}