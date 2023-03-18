import React from "react";
import useInputGroupList from "../Hooks/useInputGroupList";

export default function (
    {
        inputNames = [],
        divClass = 'd-flex mb-2',
        inputClass = 'form-control me-3',
        btnRemoveClass = 'btn btn-primary text-white ms-2',
        btnAddClass = 'shadow1 btn btn-primary text-white w-100 mt-4',
        btnAddText = 'Ajouter un ingredient'
    })
{
    const {inputs, handleChange, handleRemove, handleAdd} = useInputGroupList(inputNames)

    return (
        inputs.map((value, key) => {
            return (
                <>
                    <div className={divClass}>
                        {inputNames.map((v) => {
                            return (
                                <input
                                    data-index={key}
                                    data-inputname={v}
                                    onChange={handleChange}
                                    className={inputClass}
                                    type={'text'}
                                    value={v.name}
                                />
                            )
                        })}
                        <button className={btnRemoveClass} data-index={index} onClick={handleRemove}>X</button>
                    </div>
                    <button className={btnAddClass} onClick={handleAdd}>{btnAddText}</button>
                </>
            )
        })
    )
}