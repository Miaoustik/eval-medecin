import React from "react";

export default function InputGroupListWithSuggestions (
    {
        inputs,
        handleAdd,
        handleChange,
        handleBlur,
        handleFocus,
        suggestions,
        handleRemove,
        handleClick,
        handleMouseDown,
        suggestionActive,
        inputNames = [],
        suggestionOn = [],
        divClass = 'd-flex mb-2',
        inputClass = 'form-control me-3',
        btnRemoveClass = 'btn btn-primary text-white ms-2',
        btnAddClass = 'shadow1 btn btn-primary text-white w-100 mt-4',
        btnAddText = 'Ajouter un ingredient'
    })
{

    return (
        <>
            {inputs.map((value, index) => {
                return (
                    <div key={value.id} className={divClass}>
                        {inputNames.map((v) => {
                            if (suggestionOn.includes(v)) {
                                return (
                                    <div className={"position-relative w-100"}>
                                        <input onBlur={handleBlur} onFocus={handleFocus} data-id={value.id} data-index={index} data-inputname={v} onChange={handleChange} className={'form-control'} type={'text'} value={value[v]}/>
                                        {!((suggestions.length === 1) && (suggestions[0][v] === value[v])) && value[v] !== '' && suggestions.length > 0 &&
                                            <div className={'position-absolute w-100 bg-white rounded-bottom shadow1 mt-2' + (suggestionOn ? ' border border-primary' : '')}>
                                                {suggestionActive == value.id && suggestions.map((e, k) => {
                                                    return (
                                                        <p role={'button'} onMouseDown={handleMouseDown} onClick={handleClick} data-item={e[v]}
                                                           data-id={index}
                                                           className={'text-secondary textNoto pt-2 ps-4' + (k !== 0 ? ' border-top border-primary' : '')}
                                                           key={e[v]}>{e[v]}</p>
                                                    )
                                                })}
                                            </div>}
                                    </div>)
                            } else {
                                return <input
                                    data-index={index}
                                    data-inputname={v}
                                    onChange={handleChange}
                                    className={inputClass}
                                    type={'text'}
                                    value={value[v]}
                                    key={value.id + v}
                                />
                            }
                        })}
                        {inputs.length > 1 && <button className={btnRemoveClass} data-index={index} onClick={handleRemove}>X</button>}
                    </div>
                )
            })}
            <button className={btnAddClass} onClick={handleAdd}>{btnAddText}</button>
        </>
    )
}