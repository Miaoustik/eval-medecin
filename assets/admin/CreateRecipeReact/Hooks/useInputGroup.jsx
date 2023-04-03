import {useCallback, useState} from "react";

export default function (initialValues = []) {
    let initialValue = [{}]
    initialValue[0]['id'] = 1

    initialValues.forEach(e => {
        initialValue[0][e] = ''
    })

    const [state, setState] = useState(initialValue)

    const handleChange = useCallback((e) => {
        setState(prevState => {
            const newState = [...prevState]
            newState.forEach((value, index) => {
                if (value.id == e.target.id) {
                    newState[index][e.target.name] = e.target.value
                }
            })
            return newState
        })
    }, [])

    const handleRemove = useCallback((e) => {
        setState(prevState => {
            const newState = [...prevState]
            newState.forEach((value, index) => {
                if (value.id == e.target.name) {
                    newState.splice(index, 1)
                }
            })
            return newState
        })
    }, [])

    const handleAdd = useCallback((e) => {
        e.preventDefault()
        setState(prevState => {
            const newState = [...prevState]
            const lastId = newState[newState.length - 1].id
            const newObj = {
                id: lastId + 1
            }
            if (('error' in newState[newState.length - 1])) {
                newObj['error'] = ''
            }
            initialValues.forEach(e => {
                newObj[e] = ''
            })
            newState.push(newObj)
            return newState
        })
    }, [])

    return {
        state,
        handleChange,
        handleAdd,
        handleRemove,
        setState
    }
}