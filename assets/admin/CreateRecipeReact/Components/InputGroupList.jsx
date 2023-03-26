import React from "react";

export default function (
    {
        divClass = 'd-flex mb-2',
        inputClass = 'form-control',
        btnRemoveClass = 'btn btn-primary text-white ms-3 shadow1',
        btnAddClass = 'shadow1 btn btn-primary text-white w-100 mt-4',
        btnAddText = 'Ajouter un ingredient',
        inputs,
        handleChange,
        handleRemove,
        handleAdd,
        inputNames = [],
        type = 'text',
        firstRequired,
        containerClass = 'mt-4',
        placeholders = [],
        first
    })
{
    return (
                <div className={containerClass}>
                    {((inputs.length > 1) || (inputs.length === 1 && first === true)) && inputs.map((value, index) => {
                        return (
                            <React.Fragment key={value.id + 'input'} >
                                <div className={divClass}>
                                    {inputNames.map((v, k) => {
                                        if (type === 'textarea') {
                                            return (
                                                <textarea
                                                    key={value.id + v}
                                                    data-index={index}
                                                    data-inputname={v}
                                                    onChange={handleChange}
                                                    className={inputClass + ' ' + (k === inputNames.length - 1 ? '' : 'me-3')}
                                                    value={value[v]}
                                                    placeholder={placeholders[k]}
                                                >
                                                </textarea>
                                            )
                                        }
                                        return (
                                            <React.Fragment key={value.id + v}>
                                                <input
                                                    data-index={index}
                                                    data-inputname={v}
                                                    onChange={handleChange}
                                                    className={inputClass  + ' ' + (k === inputNames.length - 1 ? '' : 'me-3') + (value.error !== '' ? ' is-invalid' : '') + ' ingredient'}
                                                    type={type}
                                                    value={value[v]}
                                                    placeholder={placeholders[k]}
                                                    id={index + 'error'}
                                                />
                                            </React.Fragment>
                                        )
                                    })}
                                    {(inputs.length > 1 || firstRequired === false)  && <button className={btnRemoveClass} data-index={index}
                                             onClick={handleRemove}>X</button>}
                                </div>
                                <div id={index + "error"} className="invalid-feedback">
                                    {value.error}
                                </div>
                            </React.Fragment>
                        )
                    })}
                    <button className={btnAddClass} onClick={handleAdd}>{btnAddText}</button>
                </div>
    )
}