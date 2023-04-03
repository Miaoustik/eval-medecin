import {useCallback, useMemo, useState} from "react";

export default function (initialValue = {
    value: ''
}) {

    const [state, setState] = useState(initialValue)

    const handleChange = useCallback((e) => {
        setState(prevState => {
            const newState = {...prevState}
            newState.value = e.target.value
            return newState
        })
    }, [setState])

    return useMemo(() => ({
        state,
        setState,
        handleChange
    }), [
        state,
        setState,
        handleChange
    ])
}