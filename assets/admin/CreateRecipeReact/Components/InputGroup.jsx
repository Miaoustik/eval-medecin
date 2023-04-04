import React from "react";

export default function ({state, handleRemove, handleChange, withBtnRemove = true, placeholders, withValidation = false, handleBlur = null, validationOn = null, required = false}) {
    return (
        <>
            <div className={'input-group mb-2'}>
                {Object.keys(state).map((e, k) => {
                    if (e !== 'id' && e !== 'error') {
                        if (withValidation && e === validationOn) {
                            return (
                                <input required={required} onBlur={handleBlur} key={state.id + e} type={'text'} placeholder={e === 'quantity' ? placeholders[0] : placeholders[1]} onChange={handleChange} className={'form-control ' + (state.error !== '' ? (state.error === 'ok' ? 'is-valid' : 'is-invalid') : '')}   data-id={state.id} name={e} value={state[e]} />
                            )
                        } else {
                            return <input required={required} type={'text'} placeholder={e === 'quantity' ? placeholders[0] : placeholders[1]} onChange={handleChange} className={'form-control'} key={state.id + e} data-id={state.id} name={e} value={state[e]} />
                        }
                    }
                })}
                {withBtnRemove &&
                    <button name={state.id} onClick={handleRemove} className={'btn btn-outline-secondary'}>X</button>
                }
            </div>
            {withValidation && state.error !== '' && state.error !== 'ok' &&
                <div id={state.id} className={'invalid-feedback'}>{state.error}</div>
            }
        </>

    )
}