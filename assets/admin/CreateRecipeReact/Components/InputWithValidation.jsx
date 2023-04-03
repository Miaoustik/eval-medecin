import React from "react";
import Input from "./Input";

export default function ({value, error = '', type = 'text', label, handleChange, handleBlur, name = null, rows = null, required = null}) {
    return (
        <>
            <Input value={value} error={error} type={type} label={label} handleChange={handleChange} handleBlur={handleBlur} rows={rows} required={required} addClassError={true} name={name}/>
            {error !== '' && error !== 'ok' &&
                <div id={label} className={'invalid-feedback mb-4'}>{error}</div>
            }
        </>
    )
}