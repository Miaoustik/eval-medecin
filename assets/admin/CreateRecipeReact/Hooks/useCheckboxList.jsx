import {useCallback, useState} from "react";

export default function (initialValues = []) {

    let initialValue = {}
    initialValues.forEach(e => {
        initialValue[e] = false
    })

    const [state, setState] = useState(initialValue)

    const handleChange = useCallback((e) => {
        setState(prevState => {
            const newState = {...prevState}
            newState[e.target.id] = e.target.checked
            return newState
        })
    }, [setState])

    return {
        state,
        setState,
        handleChange
    }
}