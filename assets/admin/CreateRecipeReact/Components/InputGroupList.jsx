import React from 'react';
import InputGroup from "./InputGroup";

export default function ({state, ingredientsPlaceholder, addBtnText = 'Ajouter', handleChange, handleAdd, handleRemove, handleBlur, validationOn = null, required = null}) {
    return (
        <>
            {state.map((e, k) => {
                    return <InputGroup required={required} validationOn={validationOn} handleBlur={handleBlur} placeholders={ingredientsPlaceholder} handleChange={handleChange} state={e} key={e.id} withBtnRemove={k != 0 || state.length > 1} handleRemove={handleRemove} withValidation={true}/>
            })}
            <button onClick={handleAdd} className={'btn btn-primary text-white w-100 mt-4 shadow1'}>{addBtnText}</button>
        </>
    )
}