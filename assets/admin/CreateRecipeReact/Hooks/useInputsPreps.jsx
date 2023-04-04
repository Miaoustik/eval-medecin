import {useCallback, useMemo, useState} from "react";

export default function () {

    const [state, setState] = useState({
        preparation: '',
        cuisson: '',
        repos: ''
    })

    const handleChange = useCallback((e) => {
        setState(prevState => {
            const newState = {...prevState}
            newState[e.target.name] = e.target.value
            return newState
        })
    }, [setState])

    return useMemo(() => ({
        state,
        handleChange,
        setState
    }), [
        state,
        handleChange
    ])

}