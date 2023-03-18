import React from "react";

export default function (
    {
        divClass = 'd-flex mb-2',
        inputClass = 'form-control',
        btnRemoveClass = 'btn btn-primary text-white ms-3',
        btnAddClass = 'shadow1 btn btn-primary text-white w-100 mt-4',
        btnAddText = 'Ajouter un ingredient',
        inputs,
        handleChange,
        handleRemove,
        handleAdd,
        inputNames = [],
        type = 'text',
        firstRequired,
        containerClass = 'mt-4'
    })
{
    return (
                <div className={containerClass}>
                    {inputs.map((value, index) => {
                        return (
                            <div key={value.id} className={divClass}>
                                {inputNames.map((v) => {
                                    if (type === 'textarea') {
                                        return (
                                            <textarea
                                                key={value.id + v}
                                                data-index={index}
                                                data-inputname={v}
                                                onChange={handleChange}
                                                className={inputClass}
                                                value={value[v]}
                                            >
                                            </textarea>
                                        )
                                    }
                                    return (
                                        <input
                                            key={value.id + v}
                                            data-index={index}
                                            data-inputname={v}
                                            onChange={handleChange}
                                            className={inputClass}
                                            type={type}
                                            value={value[v]}
                                        />
                                    )
                                })}
                                {(inputs.length > 1 || firstRequired === false)  && <button className={btnRemoveClass} data-index={index}
                                         onClick={handleRemove}>X</button>}
                            </div>
                        )
                    })}
                    <button className={btnAddClass} onClick={handleAdd}>{btnAddText}</button>
                </div>
    )
}