import React from "react";
import InputWithValidation from "./InputWithValidation";

export default function ({state, handleChange, handleBlur, rows = null, addBtnText, handleAdd, required = null}) {
    return (
        <>
            {state.map(s => {
                return <InputWithValidation required={required} type={'textarea'} handleChange={handleChange} key={s.nameId + s.id} name={s.id} value={s.value} error={s.error} addClassError={true} handleBlur={handleBlur} rows={rows} />
            })}
            <button onClick={handleAdd} className={'btn btn-primary text-white w-100 mt-4 shadow1'}>{addBtnText}</button>
        </>
    )
}