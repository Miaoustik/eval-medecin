import React, {useCallback} from "react";

export default function ({value, type = 'text', label = null, handleChange, handleBlur, error = '', name = null, rows = null, required = false}) {

    const noWheel = useCallback((e) => {
        e.target.blur()
    }, [])

    return (
        <>
            {type === 'text' &&
                <>
                    <label className="form-label text-secondary mt-4" htmlFor={label}>{label}</label>
                    <input required={required} type={type} onChange={handleChange} onBlur={handleBlur} id={label} className={"form-control " + (error !== '' ? (error === 'ok' ? 'is-valid' : 'is-invalid') : '')} value={value}  />
                </>
            }
            {type === 'textarea' &&
                <>
                    {label &&
                        <label className="form-label text-secondary mt-4" htmlFor={label}>{label}</label>
                    }
                    <textarea required={required} id={name ?? label} onChange={handleChange} onBlur={handleBlur} rows={rows ?? 5} className={"form-control " + (error !== '' ? (error === 'ok' ? 'is-valid mb-3' : 'is-invalid') : ' mb-3')} value={value} name={name} />
                </>
            }
            {type === 'number' &&
                <>
                    <label className="form-label mt-4 text-secondary" htmlFor={name}>{label}</label>
                    <input required={required} onWheel={noWheel} min={0} name={name} onChange={handleChange} type="number" id={name} className="form-control" value={value}/>
                </>
            }
            {type === 'checkbox' &&

                <div className="ps-5 py-1 form-check">
                    <input required={required} name={label} onChange={handleChange} className="form-check-input" type="checkbox" id={label} checked={label}/>
                    <label htmlFor={label} className="form-check-label text-secondary textNoto ps-2">{label}</label>
                </div>
            }
        </>
    )
}