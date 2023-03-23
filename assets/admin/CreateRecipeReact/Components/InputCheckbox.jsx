import React from "react";

export default function InputCheckbox ({inputs = [], handleChange}) {
    return inputs.map((value, index) => {
        return <div key={value.name} className="ps-5 py-1 form-check">
            <input onChange={handleChange} data-index={index} className="form-check-input" type="checkbox" id={value.name} checked={value.checked}/>
            <label htmlFor={value} className="form-check-label text-secondary textNoto ps-2">{value.name}</label>
        </div>
    })
}