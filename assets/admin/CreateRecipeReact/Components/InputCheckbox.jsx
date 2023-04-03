import React from "react";

export default function InputCheckbox ({label, checked, handleChange}) {
    return (
        <div className="ps-5 py-1 form-check">
            <input onChange={handleChange} className="form-check-input" type="checkbox" id={label} checked={checked}/>
            <label htmlFor={label} className="form-check-label text-secondary textNoto ps-2">{label}</label>
        </div>
    )
}