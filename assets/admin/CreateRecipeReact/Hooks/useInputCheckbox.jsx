import {useCallback, useMemo, useState} from "react";

export default function useInputCheckbox (items = []) {



    const [inputs, setInputs ] = useState(items)

    const handleChange = useCallback((e) => {
        setInputs(prevState => {
            const newState = [...prevState]
            newState[e.target.getAttribute('data-index')]['checked'] = !newState[e.target.getAttribute('data-index')]['checked']
            return newState
        })
    }, [])


    return useMemo(() => ({
        inputs,
        handleChange,
        setInputs
    }), [items, inputs, handleChange, setInputs])
}