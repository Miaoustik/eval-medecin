import {useCallback, useMemo, useState} from "react";

export default function (value = '') {

    const initialValue = {
        label: '',
        checked: false
    }
    initialValue[value] = false

    const [state, setState] = useState(initialValue)

    const handleChange = useCallback((e) => {
        setState(prevState => {
            const newState = {...prevState}
            newState.checked = e.target.checked
            return newState
        })
    }, [])

    return useMemo(() => ({
        state,
        handleChange
    }), [
        state.label,
        state.checked,
        handleChange
    ])
}